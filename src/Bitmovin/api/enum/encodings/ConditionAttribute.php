<?php


namespace Bitmovin\api\enum\encodings;


use Bitmovin\api\enum\AbstractEnum;

class ConditionAttribute extends AbstractEnum
{
    const HEIGHT = 'HEIGHT';
    const WIDTH = 'WIDTH';
    const FPS = 'FPS';
    const BITRATE = 'BITRATE';
    const ASPECTRATIO = 'ASPECTRATIO';
    const INPUTSTREAM = 'INPUTSTREAM';
}