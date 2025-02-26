<?php

namespace Kalimeromk\Apollo;

use Illuminate\Support\ServiceProvider;

class ApolloServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/apollo.php', 'apollo');
    }


    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/apollo.php' => config_path('apollo.php'),
        ], 'config');
    }
}