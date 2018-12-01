<?php

namespace Bitmovin\api\enum\codecConfigurations;

use Bitmovin\api\enum\AbstractEnum;

class AC3ChannelLayout extends AbstractEnum
{
    const NONE = 'NONE';
    const MONO = 'MONO';
    const STEREO = 'STEREO';
    const SURROUND = 'SURROUND';
    const QUAD = 'QUAD';
    const CL_2_1 = '2.1';
    const CL_2_2 = '2.2';
    const CL_3_1 = '3.1';
    const CL_4_0 = '4.0';
    const CL_4_1 = '4.1';
    const CL_5_0 = '5.0';
    const CL_5_1 = '5.1';
    const CL_5_0_BACK = '5.0_BACK';
    const CL_5_1_BACK = '5.1_BACK';
}
