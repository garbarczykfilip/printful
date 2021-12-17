<?php

namespace App\Services;

use GuzzleHttp\Client;

class ShippingService
{
    const CACHE_DURATION = 5 * 60; // 5 minutes

    /**
     * @var CacheService
     */
    private CacheService $cacheService;

    /**
     * @param CacheService $cacheService
     */
    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * @param array $parameters
     * @return array|null
     */
    private function getRecordsFromCache(array $parameters): ?array
    {
        $key = $this->cacheService->generateKey($parameters);
        if (false === $key) {
            // log error info about occurred issue with information about given parameters when getting from cache
            return null;
        }
        return $this->cacheService->get($key);
    }

    /**
     * @param array $parameters
     * @return array|null
     */
    private function getRecordsFromExternalUri(array $parameters): ?array
    {
        $client = new Client();
        $remoteApi = new RemoteApi($client);
        $response = $remoteApi->post($parameters);
        $decodedResponse = json_decode($response->getBody()->getContents(), true);

        if (false === isset($decodedResponse['code']) || $decodedResponse['code'] !== RemoteApi::HTTP_OK) {
            // possibly log info about this
            return null;
        }

        $result = $decodedResponse['result'] ?? null;
        if (false === is_null($result)) {
            $key = $this->cacheService->generateKey($parameters);
            if (is_string($key)) {
                $this->cacheService->set($key, $result, self::CACHE_DURATION);
            } else {
                // log error info about occured issue with information about given parameters when setting cache
            }
        }

        return $result;
    }

    /**
     * @param array $parameters
     * @return false|string
     */
    public function getRates(array $parameters)
    {
        if (count($parameters) <= 0) {
            return json_encode([
                'code' => RemoteApi::HTTP_NO_CONTENT,
                'message' => 'No content',
            ]);
        }

        try {
            $records = $this->getRecordsFromCache($parameters);
            if (false === is_array($records)) {
                $records = $this->getRecordsFromExternalUri($parameters);
            }

            if (is_null($records)) {
                return json_encode([
                    'code' => RemoteApi::HTTP_NO_CONTENT,
                    'message' => 'No content',
                ]);
            }

            return json_encode($records);
        } catch (\Throwable $exception) {
            return json_encode([
                'code' => RemoteApi::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Internal server error',
            ]);
        }
    }
}