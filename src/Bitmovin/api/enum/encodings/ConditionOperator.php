<?php


namespace Bitmovin\api\enum\encodings;


use Bitmovin\api\enum\AbstractEnum;

class ConditionOperator extends AbstractEnum
{
    const LESS_THAN = '<';
    const LESS_THAN_OR_EQUAL_TO = '<=';
    const GREATER_THAN = '>';
    const GREATER_THAN_OR_EQUAL_TO = '>=';
    const EQUAL = '==';
    const NOT_EQUAL = '!=';
}