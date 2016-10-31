<?php

namespace Bitmovin\api\model\encodings;

use Bitmovin\api\model\AbstractModel;
use JMS\Serializer\Annotation as JMS;

class Encoding extends AbstractModel
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
     * @JMS\Type("string")
     * @var string
     */
    private $encoderVersion;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $cloudRegion;

    public function __construct($name)
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

    /**
     * @return string
     */
    public function getEncoderVersion()
    {
        return $this->encoderVersion;
    }

    /**
     * @param string $encoderVersion
     */
    public function setEncoderVersion($encoderVersion)
    {
        $this->encoderVersion = $encoderVersion;
    }

    /**
     * @return string
     */
    public function getCloudRegion()
    {
        return $this->cloudRegion;
    }

    /**
     * @param string $cloudRegion
     */
    public function setCloudRegion($cloudRegion)
    {
        $this->cloudRegion = $cloudRegion;
    }

}