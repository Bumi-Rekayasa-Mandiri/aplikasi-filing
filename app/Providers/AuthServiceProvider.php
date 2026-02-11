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

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Surat::class => SuratPolicy::class,
        ArsipSurat::class => ArsipSuratPolicy::class,
        Role::class => RolePolicy::class,
        ArsipSertifikat::class => ArsipSertifikatPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();//
    }
}
