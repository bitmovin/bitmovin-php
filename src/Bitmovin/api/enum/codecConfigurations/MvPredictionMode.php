<?php

namespace Bitmovin\api\enum\codecConfigurations;

use Bitmovin\api\enum\AbstractEnum;

class MvPredictionMode extends AbstractEnum
{
    const NONE = 'NONE';
    const AUTO = 'AUTO';
    const SPATIAL = 'SPATIAL';
    const TEMPORAL = 'TEMPORAL';
}