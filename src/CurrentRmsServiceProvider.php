<?php

namespace Wjbecker\CurrentRms;

use Wjbecker\CurrentRms\Client\Auth\ApiKeyAuth;
use Wjbecker\CurrentRms\Client\Auth\AuthManager;
use Wjbecker\CurrentRms\Client\Auth\TokenStorage;
use Wjbecker\CurrentRms\Client\CurrentRmsClient;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class CurrentRmsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__.'/../config/current-rms.php',
            'current-rms'
        );

        // Register TokenStorage
        $this->app->singleton(TokenStorage::class);

        // Register AuthManager based on config
        $this->app->singleton(AuthManager::class, function ($app) {
            $authType = config('current-rms.auth.type');
            $subdomain = config('current-rms.auth.subdomain');

            return match ($authType) {
                'api_key' => new ApiKeyAuth(
                    subdomain: $subdomain,
                    apiToken: config('current-rms.auth.api_key.token')
                ),
                default => throw new InvalidArgumentException(
                    "Invalid auth type: {$authType}. Only 'api_key' is supported in Phase 1."
                ),
            };
        });

        // Register CurrentRmsClient
        $this->app->singleton(CurrentRmsClient::class, function ($app) {
            return new CurrentRmsClient(
                baseUrl: config('current-rms.api.base_url'),
                auth: $app->make(AuthManager::class),
                timeout: config('current-rms.api.timeout', 30),
                connectTimeout: config('current-rms.api.connect_timeout', 10),
                verifySsl: config('current-rms.api.verify_ssl', true),
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/current-rms.php' => config_path('current-rms.php'),
            ], 'current-rms-config');
        }
    }
}
