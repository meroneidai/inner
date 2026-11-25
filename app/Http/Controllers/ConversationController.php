<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\AiUsageLog;
use App\Models\DreamEntry;
use App\Services\AI\AIManager;
use App\Services\AI\AiSettings;
use App\Services\Profile\ProfileContextBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(Request $request): View
    {
        $conversations = $request->user()->conversations()->with('dream')->latest()->paginate(12);

        return view('conversations.index', compact('conversations'));
    }

    public function create(Request $request): View
    {
        $dreams = $request->user()->dreams()->latest('dream_date')->limit(30)->get();
        $mode = $request->query('mode', 'text');
        $selectedDreamId = $request->query('dream');

        return view('conversations.create', compact('dreams', 'mode', 'selectedDreamId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'dream_entry_id' => ['nullable', 'exists:dream_entries,id'],
            'title' => ['nullable', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:5000'],
            'mode' => ['nullable', 'in:text,voice,deep'],
        ]);

        if (! empty($data['dream_entry_id'])) {
            $dream = DreamEntry::query()->findOrFail($data['dream_entry_id']);
            abort_unless($dream->user_id === $request->user()->id, 403);
        }

        $mode = $data['mode'] ?? 'text';
        $title = $data['title'] ?: match ($mode) {
            'voice' => __('حوار صوتي داعم'),
            'deep' => __('حوار عميق بلطف'),
            default => __('حوار حول الحلم'),
        };

        $conversation = AiConversation::query()->create([
            'user_id' => $request->user()->id,
            'dream_entry_id' => $data['dream_entry_id'] ?? null,
            'title' => $title,
        ]);

        AiMessage::query()->create([
            'ai_conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $data['message'],
        ]);

        $this->generateAssistantReply($conversation, $request->user(), app(AIManager::class), $mode);

        return redirect()->route('conversations.show', $conversation);
    }

    public function show(AiConversation $conversation): View
    {
        abort_unless($conversation->user_id === auth()->id(), 403);
        $conversation->load(['messages', 'dream']);

        return view('conversations.show', compact('conversation'));
    }

    public function reply(Request $request, AiConversation $conversation, AIManager $ai): RedirectResponse
    {
        abort_unless($conversation->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
            'mode' => ['nullable', 'in:text,voice,deep'],
        ]);

        AiMessage::query()->create([
            'ai_conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $data['message'],
        ]);

        $this->generateAssistantReply(
            $conversation->fresh('messages'),
            $request->user(),
            $ai,
            $data['mode'] ?? 'text'
        );

        return back();
    }

    private function generateAssistantReply(AiConversation $conversation, $user, AIManager $ai, string $mode = 'text'): void
    {
        if (! AiSettings::isConfigured()) {
            AiMessage::query()->create([
                'ai_conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => __('مزود الذكاء الاصطناعي غير مُعد بعد. أضف OPENROUTER_API_KEY في ملف .env أو من إعدادات الأدمن.'),
            ]);

            return;
        }

        $dreamContext = $conversation->dream?->displayText();
        $contextDream = $conversation->dream
            ?: $user->dreams()->latest('dream_date')->first()
            ?: DreamEntry::query()->make([
                'user_id' => $user->id,
                'original_text' => '',
                'dream_date' => now()->toDateString(),
            ]);
        $profileBits = app(ProfileContextBuilder::class)->forDream(
            $user,
            $contextDream,
            emotions: array_filter([(string) ($user->profile?->emotional_state ?? '')])
        );

        $system = match ($mode) {
            'voice', 'deep' => 'You are InnerPath AI — a warm voice-friendly companion for deep but safe reflection. Speak in short spoken-friendly Arabic sentences. Help the person explore pressures, emotions, and needs using ONLY authorized profile/dream context. Co-create practical gentle solutions. Never diagnose. Never shame. Ask one clarifying question at a time. Offer love/confidence/calm support when needs appear.',
            default => 'You are InnerPath AI follow-up companion. Clarify dream meaning with the user. Prefer Arabic. Never diagnose. Be warm and precise.',
        };

        $messages = [
            [
                'role' => 'system',
                'content' => $system
                    ."\nDream context: ".($dreamContext ?: 'none')
                    ."\nAuthorized profile context JSON: ".json_encode($profileBits['items'] ?? [], JSON_UNESCAPED_UNICODE)
                    ."\nExcluded reasons: ".json_encode($profileBits['excluded_reasons'] ?? [], JSON_UNESCAPED_UNICODE),
            ],
        ];

        foreach ($conversation->messages()->orderBy('id')->get() as $message) {
            $messages[] = [
                'role' => $message->role === 'assistant' ? 'assistant' : 'user',
                'content' => $message->content,
            ];
        }

        try {
            $result = $ai->driver()->chat($messages, [
                'response_format' => false,
                'model' => AiSettings::get('chat_model') ?: config('ai.openrouter.model'),
            ]);

            AiMessage::query()->create([
                'ai_conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $result['content'],
            ]);

            AiUsageLog::query()->create([
                'user_id' => $user->id,
                'provider' => AiSettings::provider(),
                'model' => $result['model'],
                'operation' => 'conversation',
                'prompt_tokens' => $result['usage']['prompt_tokens'],
                'completion_tokens' => $result['usage']['completion_tokens'],
                'total_tokens' => $result['usage']['total_tokens'],
                'status' => 'success',
            ]);
        } catch (\Throwable $e) {
            AiMessage::query()->create([
                'ai_conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => __('تعذر الحصول على رد الآن: :error', ['error' => $e->getMessage()]),
            ]);
        }
    }
}
