<?php

namespace Bitmovin\api\enum;

class ResponseStatus
{
    const ERROR = 'ERROR';
    const SUCCESS = 'SUCCESS';

    /**
     * @return array(string)
     */
    public static function getAvailableValues()
    {
        $reflection = new \ReflectionClass(get_called_class());

        return array_values($reflection->getConstants());
    }
}