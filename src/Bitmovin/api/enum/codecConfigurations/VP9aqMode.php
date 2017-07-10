<?php

namespace Bitmovin\api\enum\codecConfigurations;

use Bitmovin\api\enum\AbstractEnum;

class VP9aqMode extends AbstractEnum
{
    const NONE = 'NONE';
    const VARIANCE = 'VARIANCE';
    const COMPLEXITY = 'COMPLEXITY';
    const CYCLIC = 'CYCLIC';
}