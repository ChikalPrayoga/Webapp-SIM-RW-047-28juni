<?php

namespace App\Listeners;

class CreateInternalNotification
{
    public function handle($event): void
    {
        // TODO: [Phase 3] Implement Notification Module Integration
        // FIXME: Currently an official placeholder to ensure Event dispatcher does not fail.
        // No action is taken here until the Notification schema is ready.
        
        \Log::info('CreateInternalNotification listener triggered', ['event_class' => get_class($event)]);
    }
}
