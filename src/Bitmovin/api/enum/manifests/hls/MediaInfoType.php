<?php

namespace Bitmovin\api\enum\manifests\hls;

use Bitmovin\api\enum\AbstractEnum;

class MediaInfoType extends AbstractEnum
{
    const AUDIO = 'AUDIO';
    const VIDEO = 'VIDEO';
    const SUBTITLES = 'SUBTITLES';
    const CLOSED_CAPTIONS = 'CLOSED_CAPTIONS';


}