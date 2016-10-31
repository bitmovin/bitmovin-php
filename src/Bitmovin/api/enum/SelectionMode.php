<?php

namespace Bitmovin\api\enum;

class SelectionMode
{
    const AUTO = 'AUTO';
    const POSITION_ABSOLUTE = 'POSITION_ABSOLUTE';
    const VIDEO_RELATIVE = 'VIDEO_RELATIVE';
    const AUDIO_RELATIVE = 'AUDIO_RELATIVE';


    /**
     * @return array(string)
     */
    public static function getAvailableValues()
    {
        $reflection = new \ReflectionClass(get_called_class());

        return array_values($reflection->getConstants());
    }
}