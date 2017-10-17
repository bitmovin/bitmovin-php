<?php

namespace Bitmovin\api\enum\codecConfigurations;

use Bitmovin\api\enum\AbstractEnum;

class ColorTransfer extends AbstractEnum
{

    const UNSPECIFIED = 'UNSPECIFIED';
    const BT709 = 'BT709';
    const GAMMA22 = 'GAMMA22';
    const GAMMA28 = 'GAMMA28';
    const SMPTE170M = 'SMPTE170M';
    const SMPTE240M = 'SMPTE240M';
    const LINEAR = 'LINEAR';
    const LOG = 'LOG';
    const LOG_SQRT = 'LOG_SQRT';
    const IEC61966_2_4 = 'IEC61966_2_4';
    const BT1361_ECG = 'BT1361_ECG';
    const IEC61966_2_1 = 'IEC61966_2_1';
    const BT2020_10 = 'BT2020_10';
    const BT2020_12 = 'BT2020_12';
    const SMPTE2084 = 'SMPTE2084';
    const SMPTE428 = 'SMPTE428';
    const ARIB_STD_B67 = 'ARIB_STD_B67';
    
}