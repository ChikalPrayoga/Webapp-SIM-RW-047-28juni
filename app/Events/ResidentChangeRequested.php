<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ResidentChangeRequest;

class ResidentChangeRequested
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $changeRequest;

    public function __construct(ResidentChangeRequest $changeRequest)
    {
        $this->changeRequest = $changeRequest;
    }
}
