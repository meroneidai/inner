<?php

namespace App\Http\Controllers;

use App\Actions\CreateDream;
use App\Actions\DispatchDreamAnalysis;
use App\Jobs\TranscribeDreamAudio;
use App\Models\DreamAudioFile;
use App\Models\DreamEntry;
use App\Models\DreamTranscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DreamController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', DreamEntry::class);

        $query = $request->user()->dreams()->latest('dream_date');

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('original_text', 'like', "%{$search}%")
                    ->orWhere('corrected_text', 'like', "%{$search}%")
                    ->orWhere('personal_notes', 'like', "%{$search}%");
            });
        }

        if ($from = $request->date('from')) {
            $query->whereDate('dream_date', '>=', $from);
        }

        if ($to = $request->date('to')) {
            $query->whereDate('dream_date', '<=', $to);
        }

        if ($mood = $request->string('mood')->toString()) {
            $query->where('mood_upon_waking', $mood);
        }

        return view('dreams.index', [
            'dreams' => $query->paginate(12)->withQueryString(),
            'filters' => $request->only(['q', 'from', 'to', 'mood']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', DreamEntry::class);

        return view('dreams.create');
    }

    public function store(Request $request, CreateDream $createDream, DispatchDreamAnalysis $dispatchAnalysis): RedirectResponse
    {
        $this->authorize('create', DreamEntry::class);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:180'],
            'dream_date' => ['required', 'date'],
            'dream_time' => ['nullable', 'date_format:H:i'],
            'original_text' => ['nullable', 'string'],
            'sleep_quality' => ['nullable', 'integer', 'min:1', 'max:10'],
            'emotional_intensity' => ['nullable', 'integer', 'min:1', 'max:10'],
            'mood_upon_waking' => ['nullable', 'string', 'max:80'],
            'personal_notes' => ['nullable', 'string'],
            'audio' => ['nullable', 'file', 'mimetypes:audio/mpeg,audio/mp4,audio/wav,audio/webm,audio/ogg,video/webm', 'max:20480'],
        ]);

        $data['entry_method'] = $request->hasFile('audio') ? 'audio' : 'text';
        $data['status'] = 'recorded';

        $dream = $createDream->handle($request->user(), $data);

        if ($request->hasFile('audio')) {
            $file = $request->file('audio');
            $disk = config('innerpath.dream_media_disk', 'local');
            $path = $file->store('dreams/'.$request->user()->id, $disk);

            $audio = DreamAudioFile::query()->create([
                'dream_entry_id' => $dream->id,
                'disk' => $disk,
                'path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size_bytes' => $file->getSize(),
            ]);

            TranscribeDreamAudio::dispatch($audio);
            $status = __('تم حفظ الحلم. جاري تفريغ الصوت ثم التحليل تلقائياً.');
        } else {
            $queued = $dispatchAnalysis->handle($dream);
            $status = $queued
                ? __('تم حفظ الحلم وبدأ التحليل تلقائياً.')
                : __('تم حفظ الحلم.');
        }

        return redirect()->route('dreams.show', $dream)->with('status', $status);
    }

    public function show(DreamEntry $dream): View
    {
        $this->authorize('view', $dream);

        $dream->load(['latestAnalysis', 'latestTranscription', 'audioFiles', 'emotions', 'symbolOccurrences.symbol']);

        return view('dreams.show', compact('dream'));
    }

    public function edit(DreamEntry $dream): View
    {
        $this->authorize('update', $dream);

        return view('dreams.edit', compact('dream'));
    }

    public function update(Request $request, DreamEntry $dream, DispatchDreamAnalysis $dispatchAnalysis): RedirectResponse
    {
        $this->authorize('update', $dream);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:180'],
            'dream_date' => ['required', 'date'],
            'dream_time' => ['nullable', 'date_format:H:i'],
            'corrected_text' => ['required', 'string'],
            'sleep_quality' => ['nullable', 'integer', 'min:1', 'max:10'],
            'emotional_intensity' => ['nullable', 'integer', 'min:1', 'max:10'],
            'mood_upon_waking' => ['nullable', 'string', 'max:80'],
            'personal_notes' => ['nullable', 'string'],
        ]);

        $hadCompletedAnalysis = $dream->analyses()->where('status', 'completed')->exists();

        $dream->update($data);

        // First-time only: if never analyzed and text now available, auto-analyze.
        if (! $hadCompletedAnalysis) {
            $dispatchAnalysis->handle($dream->fresh());
        }

        return redirect()->route('dreams.show', $dream)->with('status', __('تم تحديث الحلم.'));
    }

    public function destroy(DreamEntry $dream): RedirectResponse
    {
        $this->authorize('delete', $dream);

        foreach ($dream->audioFiles as $audio) {
            Storage::disk($audio->disk)->delete($audio->path);
        }

        $dream->delete();

        return redirect()->route('dreams.index')->with('status', __('تم حذف الحلم.'));
    }

    public function confirmTranscription(Request $request, DreamEntry $dream, DispatchDreamAnalysis $dispatchAnalysis): RedirectResponse
    {
        $this->authorize('update', $dream);

        $data = $request->validate([
            'edited_transcription' => ['required', 'string'],
        ]);

        $transcription = $dream->latestTranscription;
        if ($transcription) {
            $transcription->update([
                'edited_transcription' => $data['edited_transcription'],
                'confirmed_at' => now(),
                'status' => 'confirmed',
            ]);
        } else {
            DreamTranscription::query()->create([
                'dream_entry_id' => $dream->id,
                'edited_transcription' => $data['edited_transcription'],
                'original_transcription' => $data['edited_transcription'],
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);
        }

        $dream->update([
            'corrected_text' => $data['edited_transcription'],
            'original_text' => $dream->original_text ?: $data['edited_transcription'],
        ]);

        $queued = $dispatchAnalysis->handle($dream->fresh());

        return back()->with('status', $queued
            ? __('تم تأكيد النص وبدأ التحليل تلقائياً.')
            : __('تم تأكيد النص.'));
    }
}
