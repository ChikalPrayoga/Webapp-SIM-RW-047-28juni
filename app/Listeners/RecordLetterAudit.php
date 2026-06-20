<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\LetterStatusUpdated;
use App\Models\AuditLog;

class RecordLetterAudit
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LetterStatusUpdated $event): void
    {
        // Get the latest history to see old/new status
        $latestHistory = $event->letter->statusHistories()->latest('changed_at')->first();

        AuditLog::create([
            'user_id' => auth()->id() ?? null,
            'action' => 'UPDATE_STATUS',
            'entity_type' => 'App\Models\PengajuanSurat',
            'entity_id' => $event->letter->pengajuan_id,
            'old_values' => $latestHistory ? json_encode(['current_status' => $latestHistory->previous_status?->value ?? null]) : null,
            'new_values' => json_encode([
                'current_status' => $event->letter->current_status->value,
                'notes' => $latestHistory ? $latestHistory->notes : null
            ]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
