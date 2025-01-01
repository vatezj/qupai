<?php

namespace Plugin\Joygoldmisson\Service;
use Hashids\Hashids;

use function Hyperf\Support\env;

class HashidsHelper
{
    private static $hashids;

    public static function init()
    {
        $salt = env('HASHIDS_SALT');
        $minHashLength = 0;
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        self::$hashids = new Hashids($salt, $minHashLength, $alphabet);
    }

    public static function encode(...$numbers)
    {
        if (!self::$hashids) {
            self::init();
        }
        return self::$hashids->encode(...$numbers);
    }

    public static function decode($hash)
    {
        if (!self::$hashids) {
            self::init();
        }
        return self::$hashids->decode($hash);
    }
}

