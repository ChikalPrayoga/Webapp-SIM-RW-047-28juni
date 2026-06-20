<?php

namespace App\Listeners;

use App\Events\ComplaintStatusUpdated;
use App\Models\ComplaintStatusHistory;

class RecordComplaintStatusHistory
{
    public function handle(ComplaintStatusUpdated $event): void
    {
        ComplaintStatusHistory::create([
            'aspirasi_id' => $event->complaint->aspirasi_id,
            'actor_user_id' => $event->actor ? $event->actor->user_id : null,
            'previous_status' => $event->oldStatus,
            'new_status' => $event->newStatus,
            'notes' => $event->notes,
        ]);
    }
}
