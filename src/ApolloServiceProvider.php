<?php

namespace Kalimeromk\Apollo;

use Illuminate\Support\ServiceProvider;

class ApolloServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/apollo.php', 'apollo');

        foreach (
            [
                ApolloEnrichmentService::class,
                ApolloSearchService::class,
                ApolloAccountService::class
            ] as $service
        ) {
            $this->app->singleton($service, function ($app) use ($service) {
                return new $service($app['config']->get('apollo'));
            });
        }
    }


    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/apollo.php' => config_path('apollo.php'),
        ], 'config');
    }
}