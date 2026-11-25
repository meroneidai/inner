<?php

namespace App\Actions;

use App\Jobs\AnalyzeDream;
use App\Models\DreamAnalysis;
use App\Models\DreamEntry;
use App\Services\AI\AiSettings;

class DispatchDreamAnalysis
{
    /**
     * Queue automatic first-time analysis when text exists and no completed analysis yet.
     */
    public function handle(DreamEntry $dream, bool $force = false): bool
    {
        if (! AiSettings::autoAnalyzeEnabled() && ! $force) {
            return false;
        }

        if (blank($dream->displayText())) {
            return false;
        }

        $hasCompleted = DreamAnalysis::query()
            ->where('dream_entry_id', $dream->id)
            ->where('status', 'completed')
            ->exists();

        if ($hasCompleted && ! $force) {
            return false;
        }

        $alreadyProcessing = DreamAnalysis::query()
            ->where('dream_entry_id', $dream->id)
            ->whereIn('status', ['pending', 'processing'])
            ->exists();

        if ($alreadyProcessing && ! $force) {
            return false;
        }

        if (in_array($dream->processing_status, ['analyzing', 'completed'], true) && ! $force) {
            // completed without analysis row edge-case still blocked by hasCompleted
            if ($dream->processing_status === 'analyzing') {
                return false;
            }
        }

        $dream->update(['processing_status' => 'analyzing']);

        // afterResponse: runs without a queue worker (fixes "stuck analyzing" on local).
        // Falls back to the queue when a worker is available and QUEUE_CONNECTION != sync.
        $fresh = $dream->fresh();

        if (config('queue.default') === 'sync') {
            AnalyzeDream::dispatchSync($fresh);
        } else {
            AnalyzeDream::dispatch($fresh)->afterResponse();
        }

        return true;
    }
}
