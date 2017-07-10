<?php

namespace Bitmovin\api\enum\filters;

use Bitmovin\api\enum\AbstractEnum;

class DeinterlaceParity extends AbstractEnum
{
    const AUTO = 'AUTO';
    const TOP_FIELD_FIRST = 'TOP_FIELD_FIRST';
    const BOTTOM_FIELD_FIRST = 'BOTTOM_FIELD_FIRST';

    /**
     * @return array(string)
     */
    public static function getAvailableValues()
    {
        $reflection = new \ReflectionClass(get_called_class());

        return array_values($reflection->getConstants());
    }
}