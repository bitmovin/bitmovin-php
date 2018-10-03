<?php

namespace Bitmovin\api\model\encodings\streams;


use Bitmovin\api\enum\AbstractEnum;

class StreamDecodingErrorMode extends AbstractEnum
{
    const FAIL_ON_ERROR = 'FAIL_ON_ERROR';
    const DUPLICATE_FRAMES = 'DUPLICATE_FRAMES';
}