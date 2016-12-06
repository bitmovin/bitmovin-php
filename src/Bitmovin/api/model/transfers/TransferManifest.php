<?php

namespace Bitmovin\api\model\transfers;

use Bitmovin\api\model\AbstractModel;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\ITransfer;
use Bitmovin\api\model\manifests\IManifest;
use JMS\Serializer\Annotation as JMS;

class TransferManifest extends AbstractModel implements ITransfer
{
    /**
     * @JMS\Type("DateTime")
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @JMS\Type("DateTime")
     * @var \DateTime
     */
    private $modifiedAt;

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
     * @JMS\Type("string")
     * @var  string usage CloudRegion constants recommended
     */
    private $cloudRegion;

    /**
     * @JMS\Type("string")
     * @var  string usage CloudRegion constants recommended
     */
    private $state;

    /**
     * @JMS\Type("array<Bitmovin\api\model\encodings\helper\EncodingOutput>")
     * @var  EncodingOutput[]
     */
    private $outputs;

    /**
     * @JMS\Type("string")
     * @var  string UUID
     */
    private $manifestId;

    /**
     * TransferManifest constructor.
     *
     * @param Encoding $manifest
     */
    public function __construct(IManifest $manifest)
    {
        $this->manifestId = $manifest->getId();
    }

    /**
     * @return string
     */
    public function getManifestId()
    {
        return $this->manifestId;
    }

    /**
     * @param string $manifestId
     */
    public function setManifestId($manifestId)
    {
        $this->manifestId = $manifestId;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return \Bitmovin\api\model\encodings\helper\EncodingOutput[]
     */
    public function getOutputs()
    {
        return $this->outputs;
    }

    /**
     * @param \Bitmovin\api\model\encodings\helper\EncodingOutput[] $outputs
     */
    public function setOutputs($outputs)
    {
        $this->outputs = $outputs;
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
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }
}