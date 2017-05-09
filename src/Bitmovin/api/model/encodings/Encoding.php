<?php

namespace Bitmovin\api\model\encodings;

use Bitmovin\api\model\AbstractModel;
use Bitmovin\api\model\Transferable;
use JMS\Serializer\Annotation as JMS;

class Encoding extends AbstractModel implements Transferable
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
    private $infrastructureId;

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

    /**
     * @return string
     */
    public function getInfrastructureId()
    {
        return $this->infrastructureId;
    }

    /**
     * @param string $infrastructureId
     */
    public function setInfrastructureId($infrastructureId)
    {
        $this->infrastructureId = $infrastructureId;
    }

}