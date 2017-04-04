<?php

namespace Bitmovin\api\model\manifests;

use Bitmovin\api\model\AbstractModel;
use Bitmovin\api\model\connection\ResponseEnvelope;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\Transferable;
use JMS\Serializer\Annotation as JMS;

class AbstractManifest extends AbstractModel implements Transferable
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
    private $manifestName;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $description;

    /**
     * @JMS\Type("array<Bitmovin\api\model\encodings\helper\EncodingOutput>")
     * @var  EncodingOutput[]
     */
    private $outputs;

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
    public function getManifestName()
    {
        return $this->manifestName;
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
     * @param string $manifestName
     */
    public function setManifestName($manifestName)
    {
        $this->manifestName = $manifestName;
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


}