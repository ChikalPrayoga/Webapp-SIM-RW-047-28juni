<?php

namespace App\Events;

use App\Models\LogLaporanAspirasi;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComplaintStatusUpdated
{
    use Dispatchable, SerializesModels;

    public $complaint;
    public $oldStatus;
    public $newStatus;
    public $actor;
    public $notes;

    public function __construct(LogLaporanAspirasi $complaint, $oldStatus, $newStatus, ?User $actor = null, ?string $notes = null)
    {
        $this->complaint = $complaint;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->actor = $actor;
        $this->notes = $notes;
    }
}
