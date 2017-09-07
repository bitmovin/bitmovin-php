<?php


namespace Bitmovin\api\model\encodings\streams\condition;
use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\Discriminator(field = "type", map = {
 *     "CONDITION" : "Bitmovin\api\model\encodings\streams\condition\Condition",
 *     "OR" : "Bitmovin\api\model\encodings\streams\condition\OrConjunction",
 *     "AND" : "Bitmovin\api\model\encodings\streams\condition\AndConjunction"
 * })
 */
abstract class AbstractCondition
{

}