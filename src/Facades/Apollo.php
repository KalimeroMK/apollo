<?php

namespace Kalimeromk\Apollo\Facades;

use Illuminate\Support\Facades\Facade;
use Kalimeromk\Apollo\ApolloEnrichmentService;

class Apollo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ApolloEnrichmentService::class;
    }
}