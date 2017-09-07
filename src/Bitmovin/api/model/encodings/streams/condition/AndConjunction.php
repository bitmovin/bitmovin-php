<?php


namespace Bitmovin\api\model\encodings\streams\condition;

class AndConjunction extends AbstractConjunction
{

    /**
     * AndConjunction constructor.
     * @param AbstractCondition[] $conditions
     */
    public function __construct($conditions = array())
    {
        parent::__construct($conditions);
    }

}