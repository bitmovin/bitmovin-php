<?php

namespace Bitmovin\api\model\inputs;

use Bitmovin\api\model\AbstractModel;
use Icecave\Parity\Parity;
use JMS\Serializer\Annotation as JMS;

abstract class Input extends AbstractModel
{
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $name;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $description;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function equals(Input $input)
    {
        if (!$input instanceof self)
        {
            return false;
        }
        return Parity::isEqualTo($input, $this);
    }

}