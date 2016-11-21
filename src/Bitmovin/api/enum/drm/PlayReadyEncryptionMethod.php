<?php

namespace Bitmovin\api\enum\drm;

use Bitmovin\api\enum\AbstractEnum;

class PlayReadyEncryptionMethod extends AbstractEnum
{
    const MPEG_CENC = 'MPEG_CENC';
    const PIFF_CTR = 'PIFF_CTR';
}