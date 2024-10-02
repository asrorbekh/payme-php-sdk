<?php

namespace PaymeUz\Util;

class ApiVersion
{
    public const MINOR = 1;
    public const MAJOR = 0;
    public const PATCH = 1;

    public static function getVersion(): string
    {
        return implode('.', [self::MINOR, self::MAJOR, self::PATCH]);
    }
}