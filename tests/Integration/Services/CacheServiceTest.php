<?php

namespace Tests\Unit\Services;

use App\Services\CacheService;
use PHPUnit\Framework\TestCase;

class CacheServiceTest extends TestCase
{
    /**
     * Normally I would write 4 tests.
     * Success for get and set, then failure for get and set.
     * I can not use more packages in this exercise, so I did not mock Filesystem.
     */

    public function testSetAndGetKeySuccess()
    {
        $cacheService = new CacheService();

        $searchingParameters = [
            "recipient" => [
                "city" => "Charlotte",
                "country_code" => "US",
                "zip" => 28723,
                "state_code" => "NC",
                "address1" => "11025 Westlake Dr"
            ],
            "items" => [
                [
                    "quantity" => 1,
                    "variant_id" => 7679
                ],
            ]
        ];

        $value = json_decode('[{"id":"STANDARD","name":"Flat Rate (Estimated delivery: Aug 23\u2060\u201327) ","rate":"3.99","currency":"USD","minDeliveryDays":4,"maxDeliveryDays":8}]', true);
        $duration = 20; // in seconds
        $key = $cacheService->generateKey($searchingParameters);
        $cacheService->set($key, $value, $duration);

        $result = $cacheService->get($key);
        $this->assertEquals($value, $result);
    }
}