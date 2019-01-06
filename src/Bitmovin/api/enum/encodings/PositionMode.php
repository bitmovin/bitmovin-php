<?php


namespace Bitmovin\api\enum\encodings;


use Bitmovin\api\enum\AbstractEnum;

class PositionMode extends AbstractEnum
{
    const KEYFRAME = 'KEYFRAME';
    const TIME = 'TIME';
    const SEGMENT = 'SEGMENT';
}
