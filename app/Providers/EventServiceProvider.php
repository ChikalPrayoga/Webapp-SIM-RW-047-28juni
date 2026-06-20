<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Events\ResidentChangeRequested::class => [
            \App\Listeners\RecordResidentChangeAuditLog::class,
        ],
        \App\Events\ComplaintSubmitted::class => [
            \App\Listeners\RecordComplaintActivity::class,
            \App\Listeners\CreateInternalNotification::class,
        ],
        \App\Events\ComplaintStatusUpdated::class => [
            \App\Listeners\RecordComplaintStatusHistory::class,
            \App\Listeners\RecordComplaintAudit::class,
        ],
        \App\Events\ComplaintAssigned::class => [
            \App\Listeners\RecordComplaintActivity::class,
            \App\Listeners\CreateInternalNotification::class,
        ],
        \App\Events\LetterSubmitted::class => [
            \App\Listeners\RecordLetterActivity::class,
        ],
        \App\Events\LetterStatusUpdated::class => [
            \App\Listeners\RecordLetterActivity::class,
            \App\Listeners\RecordLetterAudit::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
