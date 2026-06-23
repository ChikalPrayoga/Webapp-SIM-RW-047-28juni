<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\KartuKeluarga::class => \App\Policies\KartuKeluargaPolicy::class,
        \App\Models\AnggotaKeluarga::class => \App\Policies\AnggotaKeluargaPolicy::class,
        \App\Models\ResidentChangeRequest::class => \App\Policies\ResidentChangeRequestPolicy::class,
        \App\Models\Role::class => \App\Policies\RolePolicy::class,
        \App\Models\Permission::class => \App\Policies\PermissionPolicy::class,
        \App\Models\LogLaporanAspirasi::class => \App\Policies\LogLaporanAspirasiPolicy::class,
        \App\Models\ComplaintAssignment::class => \App\Policies\ComplaintAssignmentPolicy::class,
        \App\Models\PengajuanSurat::class => \App\Policies\PengajuanSuratPolicy::class,
        \App\Models\IuranType::class => \App\Policies\IuranTypePolicy::class,
        \App\Models\FinancialTransaction::class => \App\Policies\FinancialTransactionPolicy::class,
        \App\Models\CatatanIuranWarga::class => \App\Policies\CatatanIuranWargaPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if ($user->hasPermissionTo($ability)) {
                return true;
            }
        });
    }
}
