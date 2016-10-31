<?php

namespace Bitmovin\api\enum;

class HttpMethod
{
    const GET = 'GET';
    const POST = 'POST';
    const DELETE = 'DELETE';

    /**
     * @return array(string)
     */
    public static function getAvailableValues()
    {
        $reflection = new \ReflectionClass(get_called_class());

        return array_values($reflection->getConstants());
    }
}