<?php

namespace Bitmovin\api\enum\filters;

use Bitmovin\api\enum\AbstractEnum;

class DeinterlaceMode extends AbstractEnum
{
    const FRAME = 'FRAME';
    const FIELD = 'FIELD';
    const FRAME_NOSPATIAL = 'FRAME_NOSPATIAL';
    const FIELD_NOSPATIAL = 'FIELD_NOSPATIAL';

    /**
     * @return array(string)
     */
    public static function getAvailableValues()
    {
        $reflection = new \ReflectionClass(get_called_class());

        return array_values($reflection->getConstants());
    }
}