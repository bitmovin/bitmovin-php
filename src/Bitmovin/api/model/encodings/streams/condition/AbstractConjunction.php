<?php


namespace Bitmovin\api\model\encodings\streams\condition;

use JMS\Serializer\Annotation as JMS;

abstract class AbstractConjunction extends AbstractCondition
{
    /**
     * @JMS\Type("array<Bitmovin\api\model\encodings\streams\condition\AbstractCondition[]>")
     * @var AbstractCondition[]
     */
    private $conditions;

    /**
     * AbstractConjunction constructor.
     * @param AbstractCondition[] $conditions
     */
    public function __construct($conditions = array())
    {
        $this->conditions = $conditions;
    }


    /**
     * @return AbstractCondition[]
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param AbstractCondition[] $conditions
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
    }

}