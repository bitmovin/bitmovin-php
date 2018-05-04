<?php

namespace Bitmovin\api\model\encodings;

use Bitmovin\api\model\ModelInterface;
use JMS\Serializer\Annotation as JMS;

class StartEncodingRequest implements ModelInterface
{
    /**
     * @JMS\Type("Bitmovin\api\model\encodings\StartEncodingTrimming")
     * @var StartEncodingTrimming
     */
    private $trimming;

    /**
     * @var  array
     */
    private $previewDashManifests = array();

    /**
     * @var  array
     */
    private $previewHlsManifests = array();

    /**
     * @var  array
     */
    private $vodDashManifests = array();

    /**
     * @var  array
     */
    private $vodHlsManifests = array();

    /**
     * @return StartEncodingTrimming
     */
    public function getTrimming()
    {
        return $this->trimming;
    }

    /**
     * @param StartEncodingTrimming $trimming
     */
    public function setTrimming($trimming)
    {
        $this->trimming = $trimming;
    }

    /**
     * @return array
     */
    public function getPreviewDashManifests()
    {
        return $this->previewDashManifests;
    }

    /**
     * @param array $schema
     */
    public function setPreviewDashManifests($previewDashManifests)
    {
        $this->previewDashManifests = $previewDashManifests;
    }

    /**
     * @return array
     */
    public function getPreviewHlsManifests()
    {
        return $this->previewHlsManifests;
    }

    /**
     * @param array $schema
     */
    public function setPreviewHlsManifests($previewHlsManifests)
    {
        $this->previewHlsManifests = $previewHlsManifests;
    }

    /**
     * @return array
     */
    public function getVodDashManifests()
    {
        return $this->vodDashManifests;
    }

    /**
     * @param array $schema
     */
    public function setVodDashManifests($vodDashManifests)
    {
        $this->vodDashManifests = $vodDashManifests;
    }

    /**
     * @return array
     */
    public function getVodHlsManifests()
    {
        return $this->vodHlsManifests;
    }

    /**
     * @param array $schema
     */
    public function setVodHlsManifests($vodHlsManifests)
    {
        $this->vodHlsManifests = $vodHlsManifests;
    }

    public function getId()
    {
        return null;
    }

}