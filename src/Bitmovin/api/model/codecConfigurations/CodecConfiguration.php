<?php

namespace Bitmovin\api\model\codecConfigurations;

use Bitmovin\api\model\AbstractModel;
use JMS\Serializer\Annotation as JMS;

abstract class CodecConfiguration extends AbstractModel
{
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $name;
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $description;


    /**
     * Constructor.
     *
     * @param string $name
     */
    protected function __construct($name)
    {
        $this->name = $name;
    }

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

}