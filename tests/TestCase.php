<?php

namespace Tests;

use App\DotEnv;

(new DotEnv(dirname(__DIR__) . '/.env'))->load();

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $className
     * @param $methodName
     * @param array $args
     * @return mixed
     * @throws \ReflectionException
     */
    public function invokeMethod(string $className, $methodName, array $args)
    {
        $reflection = new \ReflectionClass($className);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($reflection->newInstanceWithoutConstructor(), $args);
    }
}