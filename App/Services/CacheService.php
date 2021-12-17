<?php

namespace App\Services;

use App\Interfaces\CacheInterface;
use App\Utils\ArrayUtils;

class CacheService implements CacheInterface
{
    /**
     * Store a mixed type value in cache for a certain amount of seconds.
     * Supported values should be scalar types and arrays.
     *
     * @param string $key
     * @param mixed $value
     * @param int $duration Duration in seconds
     * @return mixed
     */
    public function set(string $key, $value, int $duration)
    {
        try {
            $cacheDirectory = dirname(__DIR__, 2) . '/Cache';
            if (false === file_exists($cacheDirectory)) {
            	 // commented, because filesystem must be properly configured
                //mkdir($cacheDirectory, fileperms(__FILE__), true);
                mkdir($cacheDirectory, 0777, true);
            }

            array_map('unlink', array_filter((array)glob($cacheDirectory . '/*-' . $key)));

            if (false === is_string($value)) {
                $value = json_encode($value);
            }
            $fileContent = json_encode([
                'expires_at' => time() + $duration,
                'value' => $value,
            ]);
            return file_put_contents($cacheDirectory . '/' . $key, $fileContent);
        } catch (\Throwable $exception) {
            // possibly log exception
            return false;
        }
    }

    /**
     * Retrieve stored item.
     * Returns the same type as it was stored in.
     * Returns null if entry has expired.
     *
     * @param string $key
     * @return array|null
     */
    public function get(string $key): ?array
    {
        try {
            $cacheDirectory = dirname(__DIR__, 2) . '/Cache';
            $paths = glob($cacheDirectory . '/*' . $key);
            rsort($paths);
            $pathToNewestFile = array_shift($paths);

            if (file_exists($pathToNewestFile)) {
                $cachedRecord = json_decode(file_get_contents($pathToNewestFile), true);
                $expired = time() > $cachedRecord['expires_at'];
                if ($expired) {
                    return null;
                }

                return json_decode($cachedRecord['value'], true);
            }

            return null;
        } catch (\Throwable $exception) {
            // log exception
            return null;
        }
    }

    /**
     * @param array $parameters
     * @return false|string
     */
    public function generateKey(array $parameters)
    {
        $sortSucceeded = ArrayUtils::sortArrayRecursively($parameters);
        if (false === $sortSucceeded) {
            return false;
        }
        return md5(json_encode($parameters));
    }
}
