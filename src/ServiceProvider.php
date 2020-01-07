<?php

namespace Fouladgar\MobileVerifier;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Fouladgar\MobileVerifier\Middleware\EnsureMobileIsVerified;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @param Filesystem $filesystem
     */
    public function boot(Filesystem $filesystem): void
    {
        $this->publishes([
            __DIR__ . '/../database/migrations/create_mobile_verifications_table.php.stub' => $this->getMigrationFileName($filesystem),
        ], 'migrations');

        $this->app['router']->middleware('mobile.verified', EnsureMobileIsVerified::class);
    }

    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param Filesystem $filesystem
     * @return string
     */
    protected function getMigrationFileName(Filesystem $filesystem): string
    {
        $timestamp = date('Y_m_d_His');

        return Collection::make($this->app->databasePath() . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR)
                         ->flatMap(static function ($path) use ($filesystem) {
                             return $filesystem->glob($path . '*_create_mobile_verifications_table.php');
                         })->push($this->app->databasePath() . "/migrations/{$timestamp}_create_mobile_verifications_table.php")
                         ->first();
    }
}
