<?php


namespace Bitmovin\api\model\encodings\streams\condition;

use JMS\Serializer\Annotation as JMS;

class Condition extends AbstractCondition
{

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $attribute;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $operator;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $value;

    /**
     * Condition constructor.
     * @param string $attribute
     * @param string $operator
     * @param string $value
     */
    public function __construct($attribute = '', $operator = '', $value = '')
    {
        $this->attribute = $attribute;
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param string $attribute
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }


}