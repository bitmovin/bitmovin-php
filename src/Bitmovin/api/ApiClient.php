<?php

namespace Bitmovin\api;

use Bitmovin\api\resource\container\CodecConfigurationContainer;
use Bitmovin\api\resource\container\InputContainer;
use Bitmovin\api\resource\container\ManifestContainer;
use Bitmovin\api\resource\container\OutputContainer;
use Bitmovin\api\resource\container\TransferContainer;
use Bitmovin\api\resource\EncodingResource;

class ApiClient
{
    private $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return EncodingResource
     */
    public function encodings()
    {
        return new EncodingResource($this->getApiKey());
    }

    /**
     * @return InputContainer
     */
    public function inputs()
    {
        return new InputContainer($this->getApiKey());
    }

    /**
     * @return OutputContainer
     */
    public function outputs()
    {
        return new OutputContainer($this->getApiKey());
    }

    /**
     * @return ManifestContainer
     */
    public function manifests()
    {
        return new ManifestContainer($this->getApiKey());
    }

    /**
     * @return CodecConfigurationContainer
     */
    public function codecConfigurations()
    {
        return new CodecConfigurationContainer($this->getApiKey());
    }

    /**
     * @return TransferContainer
     */
    public function transfers() {
        return new TransferContainer($this->getApiKey());
    }

    private function getApiKey()
    {
        return $this->apiKey;
    }
}