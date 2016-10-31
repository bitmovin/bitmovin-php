<?php

namespace Bitmovin\test\api\util;

final class RegexpHelper
{
    const UUID = '/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/';

    public static function isUUID($string)
    {
        $result = preg_match(static::UUID, $string);
        return $result === 1 ? true : false;
    }
}