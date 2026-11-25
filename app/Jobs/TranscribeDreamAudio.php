<?php

namespace App\Jobs;

use App\Actions\DispatchDreamAnalysis;
use App\Models\AiUsageLog;
use App\Models\DreamAudioFile;
use App\Models\DreamTranscription;
use App\Services\Transcription\TranscriptionManager;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;

class TranscribeDreamAudio implements ShouldBeUnique, ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public DreamAudioFile $audio) {}

    public function uniqueId(): string
    {
        return 'transcribe-audio-'.$this->audio->id;
    }

    public function backoff(): array
    {
        return [20, 60, 120];
    }

    public function handle(TranscriptionManager $manager): void
    {
        $audio = $this->audio->fresh(['dreamEntry']);
        $dream = $audio->dreamEntry;

        $transcription = DreamTranscription::query()->create([
            'dream_entry_id' => $audio->dream_entry_id,
            'dream_audio_file_id' => $audio->id,
            'status' => 'processing',
            'provider' => config('ai.transcription.provider'),
        ]);

        $dream->update(['processing_status' => 'transcribing']);

        try {
            $absolute = Storage::disk($audio->disk)->path($audio->path);
            $result = $manager->driver()->transcribe($absolute, $dream->language);

            $transcription->update([
                'original_transcription' => $result['text'],
                'edited_transcription' => $result['text'],
                'detected_language' => $result['language'],
                'confidence' => $result['confidence'],
                'provider' => $result['provider'],
                'status' => 'completed',
            ]);

            if (blank($dream->original_text)) {
                $dream->update([
                    'original_text' => $result['text'],
                    'corrected_text' => $result['text'],
                    'language' => $result['language'] ?? $dream->language,
                    'processing_status' => 'transcribed',
                ]);
            } else {
                $dream->update(['processing_status' => 'transcribed']);
            }

            AiUsageLog::query()->create([
                'user_id' => $dream->user_id,
                'provider' => $result['provider'],
                'model' => config('ai.transcription.model'),
                'operation' => 'transcription',
                'status' => 'success',
                'meta' => ['dream_audio_file_id' => $audio->id],
            ]);

            // Auto first-time analysis after transcription text is available.
            app(DispatchDreamAnalysis::class)->handle($dream->fresh());
        } catch (Throwable $e) {
            $transcription->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            // Do not overwrite a completed analysis state if transcription fails later.
            if (! in_array($dream->fresh()->processing_status, ['analyzing', 'completed'], true)
                && $dream->fresh()->status !== 'analyzed') {
                $dream->update(['processing_status' => 'transcription_failed']);
            }

            throw $e;
        }
    }
}
