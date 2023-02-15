<?php

namespace KyleWLawrence\WaboxApp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \WaboxApp\Api\HttpClient
 */
class WaboxApp extends Facade
{
    /**
     * Return facade accessor.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'WaboxApp';
    }
}
