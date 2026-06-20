<?php

namespace App\Events;

use App\Models\LogLaporanAspirasi;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComplaintSubmitted
{
    use Dispatchable, SerializesModels;

    public $complaint;

    public function __construct(LogLaporanAspirasi $complaint)
    {
        $this->complaint = $complaint;
    }
}
