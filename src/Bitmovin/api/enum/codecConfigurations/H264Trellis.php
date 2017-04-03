<?php

namespace Bitmovin\api\enum\codecConfigurations;

use Bitmovin\api\enum\AbstractEnum;

class H264Trellis extends AbstractEnum
{

    const DISABLED = 'DISABLED';
    const ENABLED_FINAL_MB = 'ENABLED_FINAL_MB';
    const ENABLED_ALL = 'ENABLED_ALL';

}