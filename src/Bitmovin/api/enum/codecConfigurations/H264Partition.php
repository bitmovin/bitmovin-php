<?php

namespace Bitmovin\api\enum\codecConfigurations;

use Bitmovin\api\enum\AbstractEnum;

class H264Partition extends AbstractEnum
{

    const NONE = 'NONE';
    const P8X8 = 'P8X8';
    const P4X4 = 'P4X4';
    const B8X8 = 'B8X8';
    const I8X8 = 'I8X8';
    const I4X4 = 'I4X4';
    const ALL = 'ALL';

}