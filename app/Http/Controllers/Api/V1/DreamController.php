<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\CreateDream;
use App\Http\Controllers\Controller;
use App\Http\Resources\DreamResource;
use App\Jobs\AnalyzeDream;
use App\Jobs\TranscribeDreamAudio;
use App\Models\DreamAudioFile;
use App\Models\DreamEntry;
use Illuminate\Http\Request;

class DreamController extends Controller
{
    public function index(Request $request)
    {
        $dreams = $request->user()->dreams()->latest('dream_date')->paginate(20);

        return DreamResource::collection($dreams);
    }

    public function store(Request $request, CreateDream $createDream)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:180'],
            'dream_date' => ['required', 'date'],
            'dream_time' => ['nullable', 'date_format:H:i'],
            'original_text' => ['nullable', 'string'],
            'sleep_quality' => ['nullable', 'integer', 'min:1', 'max:10'],
            'emotional_intensity' => ['nullable', 'integer', 'min:1', 'max:10'],
            'mood_upon_waking' => ['nullable', 'string', 'max:80'],
            'personal_notes' => ['nullable', 'string'],
        ]);

        $dream = $createDream->handle($request->user(), $data + ['status' => 'recorded']);

        return new DreamResource($dream);
    }

    public function show(Request $request, DreamEntry $dream)
    {
        abort_unless($dream->user_id === $request->user()->id, 403);

        return new DreamResource($dream->load(['latestAnalysis', 'latestTranscription']));
    }

    public function update(Request $request, DreamEntry $dream)
    {
        abort_unless($dream->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:180'],
            'dream_date' => ['sometimes', 'date'],
            'corrected_text' => ['sometimes', 'string'],
            'sleep_quality' => ['nullable', 'integer', 'min:1', 'max:10'],
            'emotional_intensity' => ['nullable', 'integer', 'min:1', 'max:10'],
            'mood_upon_waking' => ['nullable', 'string', 'max:80'],
            'personal_notes' => ['nullable', 'string'],
        ]);

        $dream->update($data);

        return new DreamResource($dream);
    }

    public function destroy(Request $request, DreamEntry $dream)
    {
        abort_unless($dream->user_id === $request->user()->id, 403);
        $dream->delete();

        return response()->json(['message' => 'deleted']);
    }

    public function uploadAudio(Request $request, DreamEntry $dream)
    {
        abort_unless($dream->user_id === $request->user()->id, 403);

        $request->validate([
            'audio' => ['required', 'file', 'max:20480'],
        ]);

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

        return response()->json(['audio_id' => $audio->id, 'status' => 'queued']);
    }

    public function transcribe(Request $request, DreamEntry $dream)
    {
        abort_unless($dream->user_id === $request->user()->id, 403);
        $audio = $dream->audioFiles()->latest()->firstOrFail();
        TranscribeDreamAudio::dispatch($audio);

        return response()->json(['status' => 'queued']);
    }

    public function analyze(Request $request, DreamEntry $dream)
    {
        abort_unless($dream->user_id === $request->user()->id, 403);
        AnalyzeDream::dispatch($dream);

        return response()->json(['status' => 'queued']);
    }

    public function analysis(Request $request, DreamEntry $dream)
    {
        abort_unless($dream->user_id === $request->user()->id, 403);

        return response()->json($dream->latestAnalysis);
    }
}
