<?php

namespace App\Providers;

use App\Policies\StudentDocumentPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Modules\Mahasiswa\Models\StudentDocument;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Hanya local
        if (!app()->environment('local')) {
            return;
        }

        // Jalankan SETELAH Laravel booted
        // tapi pastikan hanya ketika phpCAS sudah di-init (setelah ada request CAS)
        app()->terminating(function () {
            // nothing
        });
        // Policy (Mahasiswa)
        Gate::policy(StudentDocument::class, StudentDocumentPolicy::class);

        // Defaults (main)
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}