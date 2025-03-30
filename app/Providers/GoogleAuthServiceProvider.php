<?php

namespace App\Providers;

use App\Services\GoogleAuthService;
use Google\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class GoogleAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(Client::class, function () {
            $client = new Client();
            $client->setAuthConfig(Storage::path(\config('auth.google_auth.credentials_file_name')));
            $client->setRedirectUri(\config('auth.google_auth.redirect_uri'));
            return $client;
        });

        $this->app->bind(GoogleAuthService::class, function ($app) {
            return new GoogleAuthService(
                $app->make(Client::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
