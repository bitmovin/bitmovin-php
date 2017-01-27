<?php


namespace Bitmovin\configs\manifest;


use Bitmovin\configs\AbstractStreamConfig;

class HlsConfigurationFileNaming
{

    /**
     * @var AbstractStreamConfig
     */
    public $configuration;

    /**
     * @var string
     */
    public $name;

    /**
     * HlsConfigurationFileNaming constructor.
     * @param AbstractStreamConfig $configuration
     * @param string               $name
     */
    public function __construct(AbstractStreamConfig $configuration, $name)
    {
        $this->configuration = $configuration;
        $this->name = $name;
    }


}