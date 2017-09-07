<?php


namespace Bitmovin\api\enum\encodings;


use Bitmovin\api\enum\AbstractEnum;

class ConditionType extends AbstractEnum
{
    const CONDITION = 'CONDITION';
    const AND_CONJUNCTION = 'AND';
    const OR_CONJUNCTION = 'OR';
}