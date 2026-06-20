<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\ActivityLog;

class RecordLetterActivity
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
    public function handle(object $event): void
    {
        if ($event instanceof \App\Events\LetterSubmitted) {
            ActivityLog::create([
                'user_id' => auth()->id() ?? null,
                'action' => 'SUBMIT_LETTER',
                'description' => "Pengajuan surat baru dengan NIK {$event->letter->nik}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
        
        if ($event instanceof \App\Events\LetterStatusUpdated) {
            $status = $event->letter->current_status->value;
            if (in_array($status, ['COMPLETED', 'REJECTED'])) {
                ActivityLog::create([
                    'user_id' => auth()->id() ?? null,
                    'action' => "{$status}_LETTER",
                    'description' => "Pengajuan surat ID {$event->letter->pengajuan_id} diubah menjadi {$status}",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        }
    }
}
