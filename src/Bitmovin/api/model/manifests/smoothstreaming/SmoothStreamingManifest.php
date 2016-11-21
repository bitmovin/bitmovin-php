<?php

namespace Bitmovin\api\model\manifests\smoothstreaming;

use Bitmovin\api\model\AbstractModel;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use JMS\Serializer\Annotation as JMS;

class SmoothStreamingManifest extends AbstractModel
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
     * @JMS\Type("string")
     * @var  string
     */
    private $serverManifestName;

    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $clientManifestName;

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
    public function getServerManifestName()
    {
        return $this->serverManifestName;
    }

    /**
     * @param string $serverManifestName
     */
    public function setServerManifestName($serverManifestName)
    {
        $this->serverManifestName = $serverManifestName;
    }

    /**
     * @return string
     */
    public function getClientManifestName()
    {
        return $this->clientManifestName;
    }

    /**
     * @param string $clientManifestName
     */
    public function setClientManifestName($clientManifestName)
    {
        $this->clientManifestName = $clientManifestName;
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