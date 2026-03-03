<?php

namespace App\Providers;

use App\Models\Surat;
use App\Policies\SuratPolicy;
use App\Policies\ArsipSuratPolicy;
use App\Models\ArsipSurat;
use App\Models\ArsipSertifikat;
use App\Policies\ArsipSertifikatPolicy;
use Spatie\Permission\Models\Role;
use App\Policies\RolePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Surat::class => SuratPolicy::class,
        ArsipSurat::class => ArsipSuratPolicy::class,
        Role::class => RolePolicy::class,
        ArsipSertifikat::class => ArsipSertifikatPolicy::class,
    ];

    public function register(): void
    {
        
    }

    public function boot(): void
    {
        $this->registerPolicies();//

        Gate::before(function ($user, $ability) {
        if ($user->hasRole('super-admin')) {
            return true;
        }
        });
    }
}