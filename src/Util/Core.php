<?php

namespace PaymeUz\Util;

/**
 * @copyright Stripe PHP sdk
 *
 * @license  The MIT License
 *
 * @copyright  (c) 2010-2019 Stripe, Inc. (https://stripe.com)
 * @copyright https://github.com/stripe/stripe-php/contributors
 * */
abstract class Core
{
    private static bool|null $isMbStringAvailable = null;
    private static bool|null $isHashEqualsAvailable = null;

    /**
     * @param mixed|null $data
     * @param bool $dump
     * @param bool $exit
     * @author self
     * @return void
     */
    public static function dd(mixed $data = null, bool $dump = false, bool $exit = true): void
    {
        echo '<pre>';
        if ($dump) {
            var_dump($data);
        } else {
            print_r($data);
        }
        echo '</pre>';

        if ($exit) {
            exit();
        }
    }

    /**
     * Whether the provided array (or other) is a list rather than a dictionary.
     * A list is defined as an array for which all the keys are consecutive
     * integers starting at 0. Empty arrays are considered to be lists.
     *
     * @param array|mixed $array
     *
     * @return bool true if the given object is a list
     */
    public static function isList(mixed $array): bool
    {
        if (!\is_array($array)) {
            return false;
        }
        if ([] === $array) {
            return true;
        }
        if (\array_keys($array) !== \range(0, \count($array) - 1)) {
            return false;
        }

        return true;
    }

    /**
     * @param mixed|string $value a string to UTF8-encode
     *
     * @return mixed|string the UTF8-encoded string, or the object passed in if
     *    it wasn't a string
     */
    public static function utf8(mixed $value): mixed
    {
        if (null === self::$isMbStringAvailable) {
            self::$isMbStringAvailable = \function_exists('mb_detect_encoding') && \function_exists(
                    'mb_convert_encoding'
                );

            if (!self::$isMbStringAvailable) {
                \trigger_error(
                    'It looks like the mbstring extension is not enabled. ' .
                    'UTF-8 strings will not properly be encoded. Ask your system ' .
                    'administrator to enable the mbstring extension, or write to ' .
                    'support@stripe.com if you have any questions.',
                    \E_USER_WARNING
                );
            }
        }

        if (\is_string($value) && self::$isMbStringAvailable && 'UTF-8' !== \mb_detect_encoding(
                $value,
                'UTF-8',
                true
            )) {
            return mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
        }

        return $value;
    }

    /**
     * Compares two strings for equality. The time taken is independent of the
     * number of characters that match.
     *
     * @param string $a one of the strings to compare
     * @param string $b the other string to compare
     *
     * @return bool true if the strings are equal, false otherwise
     */
    public static function secureCompare(string $a, string $b): bool
    {
        if (null === self::$isHashEqualsAvailable) {
            self::$isHashEqualsAvailable = \function_exists('hash_equals');
        }

        if (self::$isHashEqualsAvailable) {
            return \hash_equals($a, $b);
        }
        if (\strlen($a) !== \strlen($b)) {
            return false;
        }

        $result = 0;
        for ($i = 0; $i < \strlen($a); ++$i) {
            $result |= \ord($a[$i]) ^ \ord($b[$i]);
        }

        return 0 === $result;
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public static function encodeParameters(array $params): string
    {
        $flattenedParams = self::flattenParams($params);
        $pieces = [];
        foreach ($flattenedParams as $param) {
            list($k, $v) = $param;
            $pieces[] = self::urlEncode($k) . '=' . self::urlEncode($v);
        }

        return \implode('&', $pieces);
    }

    /**
     * @param array $params
     * @param string|null $parentKey
     *
     * @return array
     */
    public static function flattenParams(array $params, string|null $parentKey = null): array
    {
        $result = [];

        foreach ($params as $key => $value) {
            $calculatedKey = $parentKey ? "{$parentKey}[{$key}]" : $key;

            if (self::isList($value)) {
                $result = \array_merge($result, self::flattenParamsList($value, $calculatedKey));
            } elseif (\is_array($value)) {
                $result = \array_merge($result, self::flattenParams($value, $calculatedKey));
            } else {
                $result[] = [$calculatedKey, $value];
            }
        }

        return $result;
    }

    /**
     * @param array $value
     * @param string $calculatedKey
     *
     * @return array
     */
    public static function flattenParamsList(array $value, string $calculatedKey): array
    {
        $result = [];

        foreach ($value as $i => $elem) {
            if (self::isList($elem)) {
                $result = \array_merge($result, self::flattenParamsList($elem, $calculatedKey));
            } elseif (\is_array($elem)) {
                $result = \array_merge($result, self::flattenParams($elem, "{$calculatedKey}[{$i}]"));
            } else {
                $result[] = ["{$calculatedKey}[{$i}]", $elem];
            }
        }

        return $result;
    }

    /**
     * Rebuilds an array from a flattened list of key-value pairs.
     *
     * @param array $flattenedData
     * @return array
     */
    public static function rebuildParams(array $flattenedData): array
    {
        $result = [];

        foreach ($flattenedData as [$key, $value]) {
            $keys = preg_replace('/]/', '', $key);
            $keys = explode('[', $keys);

            $temp = &$result;
            foreach ($keys as $innerKey) {
                if (!isset($temp[$innerKey])) {
                    $temp[$innerKey] = [];
                }
                $temp = &$temp[$innerKey];
            }
            $temp = $value;
        }

        return $result;
    }

    /**
     * @param string $key a string to URL-encode
     *
     * @return string the URL-encoded string
     */
    public static function urlEncode(string $key): string
    {
        $s = \urlencode($key);

        // Don't use strict form encoding by changing the square bracket control
        // characters back to their literals. This is fine by the server, and
        // makes these parameter strings easier to read.
        $s = \str_replace('%5B', '[', $s);

        return \str_replace('%5D', ']', $s);
    }

    /**
     * Normalizes an ID value, extracting an 'id' field from an array if present.
     *
     * @param mixed $id The ID to normalize, or an array with an 'id' field.
     * @return array Array containing the extracted ID and remaining parameters.
     */
    public static function normalizeId(mixed $id): array
    {
        if (\is_array($id)) {
            if (!isset($id['id'])) {
                return [null, $id];
            }
            $params = $id;
            $id = $params['id'];
            unset($params['id']);
        } else {
            $params = [];
        }

        return [$id, $params];
    }

    /**
     * Returns UNIX timestamp in milliseconds.
     *
     * @return int current time in millis
     */
    public static function currentTimeMilliSeconds(): int
    {
        return (int)\round(\microtime(true) * 1000);
    }
}
