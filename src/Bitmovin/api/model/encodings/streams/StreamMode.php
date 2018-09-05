<?php

namespace Bitmovin\api\model\encodings\streams;


use Bitmovin\api\enum\AbstractEnum;

class StreamMode extends AbstractEnum
{
    const STANDARD = 'STANDARD';
    const PER_TITLE_TEMPLATE = 'PER_TITLE_TEMPLATE';
    const PER_TITLE_RESULT = 'PER_TITLE_RESULT';
}