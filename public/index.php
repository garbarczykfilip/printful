<?php

use App\DotEnv;
use App\Services\CacheService;
use App\Services\RemoteApi;
use App\Services\ShippingService;

require_once(dirname(__FILE__, 2) . "/vendor/autoload.php");

(new DotEnv(dirname(__DIR__) . '/.env'))->load();

try {
    $parameters = [
        "recipient" => [
            "address1" => "11025 Westlake Dr",
            "city" => "Charlotte",
            "country_code" => "US",
            "state_code" => "NC",
            "zip" => 28723
        ],
        "items" => [
            [
                "quantity" => 1,
                "variant_id" => 7679
            ],
        ]
    ];

    $cacheService = new CacheService();
    $service = new ShippingService($cacheService);
    echo $service->getRates($parameters);
} catch (\Throwable $e) {
    return json_encode([
        'code' => RemoteApi::HTTP_INTERNAL_SERVER_ERROR,
        'message' => 'Internal server error',
    ]);
}