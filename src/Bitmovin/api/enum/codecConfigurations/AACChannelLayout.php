<?php

namespace Bitmovin\api\enum\codecConfigurations;

use Bitmovin\api\enum\AbstractEnum;

class AACChannelLayout extends AbstractEnum
{
    const NONE = 'NONE';
    const MONO = 'MONO';
    const STEREO = 'STEREO';
    const SURROUND = 'SURROUND';
    const QUAD = 'QUAD';
    const HEXAGONAL = 'HEXAGONAL';
    const OCTAGONAL = 'OCTAGONAL';
    const STEREO_DOWNMIX = 'STEREO_DOWNMIX';
    const CL_2_1 = '2.1';
    const CL_2_2 = '2.2';
    const CL_3_1 = '3.1';
    const CL_4_0 = '4.0';
    const CL_4_1 = '4.1';
    const CL_5_0 = '5.0';
    const CL_5_1 = '5.1';
    const CL_5_0_BACK = '5.0_BACK';
    const CL_5_1_BACK = '5.1_BACK';
    const CL_6_0 = '6.0';
    const CL_6_0_FRONT = '6.0_FRONT';
    const CL_6_1 = '6.1';
    const CL_6_1_BACK = '6.0_BACK';
    const CL_6_1_FRONT = '6.1_FRONT';
    const CL_7_0 = '7.0';
    const CL_7_0_FRONT = '7.0_FRONT';
    const CL_7_1 = '7.1';
    const CL_7_1_WIDE = '7.1_WIDE';
    const CL_7_1_WIDE_BACK = '7.1_WIDE_BACK';

}