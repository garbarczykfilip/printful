<?php

namespace App\Utils;

class ArrayUtils
{
    public static function sortArrayRecursively(&$array): bool
    {
        foreach ($array as &$item) {
            if (is_array($item)) {
                $success = self::sortArrayRecursively($item);
                if (false === $success) {
                    return false;
                }
            }
        }
        return asort($array);
    }
}