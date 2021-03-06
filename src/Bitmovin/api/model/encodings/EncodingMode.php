<?php

namespace Bitmovin\api\model\encodings;


use Bitmovin\api\enum\AbstractEnum;

class EncodingMode extends AbstractEnum
{
    const STANDARD = 'STANDARD';
    const SINGLE_PASS = 'SINGLE_PASS';
    const TWO_PASS = 'TWO_PASS';
    const THREE_PASS = 'THREE_PASS';
}

