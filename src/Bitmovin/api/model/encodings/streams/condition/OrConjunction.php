<?php


namespace Bitmovin\api\model\encodings\streams\condition;

class OrConjunction extends AbstractConjunction
{

    /**
     * OrConjunction constructor.
     * @param AbstractCondition[] $conditions
     */
    public function __construct($conditions = array())
    {
        parent::__construct($conditions);
    }

}
