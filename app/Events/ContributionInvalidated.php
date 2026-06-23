<?php

namespace App\Events;

use App\Models\CatatanIuranWarga;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContributionInvalidated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public CatatanIuranWarga $catatanIuran;

    /**
     * Create a new event instance.
     */
    public function __construct(CatatanIuranWarga $catatanIuran)
    {
        $this->catatanIuran = $catatanIuran;
    }
}
