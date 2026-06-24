<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stichoza\GoogleTranslate\TranslateClient;

class TranslateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TranslateClient::class, function ($app) {
            return new TranslateClient();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
