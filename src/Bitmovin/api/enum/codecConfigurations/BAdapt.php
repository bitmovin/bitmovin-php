<?php

namespace Bitmovin\api\enum\codecConfigurations;

use Bitmovin\api\enum\AbstractEnum;

class BAdapt extends AbstractEnum
{
    const NONE = 'NONE';
    const FAST = 'FAST';
    const FULL = 'FULL';

    /**
     * @return array(string)
     */
    public static function getAvailableValues()
    {
        $reflection = new \ReflectionClass(get_called_class());

        return array_values($reflection->getConstants());
    }
}