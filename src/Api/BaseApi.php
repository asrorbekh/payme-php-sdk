<?php

namespace PaymeUz\Api;

use PaymeUz\Util\ApiVersion;

abstract class BaseApi
{
    public static function apiVersion(): string
    {
        return ApiVersion::getVersion();
    }
}