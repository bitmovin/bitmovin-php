<?php

namespace Bitmovin\api\model\encodings;

use Bitmovin\api\model\AbstractModel;
use Bitmovin\api\model\ModelInterface;
use Bitmovin\api\model\Transferable;
use JMS\Serializer\Annotation as JMS;

class StartLiveEncodingRequest implements ModelInterface
{
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $streamKey;

    /**
     * @JMS\Type("array<Bitmovin\api\model\encodings\LiveHlsManifest>")
     * @var LiveHlsManifest[]
     */
    private $hlsManifests;

    /**
     * @JMS\Type("array<Bitmovin\api\model\encodings\LiveDashManifest>")
     * @var LiveDashManifest[]
     */
    private $dashManifests;

    /**
     * @return string
     */
    public function getStreamKey()
    {
        return $this->streamKey;
    }

    /**
     * @param string $streamKey
     */
    public function setStreamKey($streamKey)
    {
        $this->streamKey = $streamKey;
    }

    /**
     * @return LiveHlsManifest[]
     */
    public function getHlsManifests()
    {
        return $this->hlsManifests;
    }

    /**
     * @param LiveHlsManifest[] $hlsManifests
     */
    public function setHlsManifests($hlsManifests)
    {
        $this->hlsManifests = $hlsManifests;
    }

    /**
     * @return LiveDashManifest[]
     */
    public function getDashManifests()
    {
        return $this->dashManifests;
    }

    /**
     * @param LiveDashManifest[] $dashManifests
     */
    public function setDashManifests($dashManifests)
    {
        $this->dashManifests = $dashManifests;
    }

    public function getId()
    {
        return null;
    }

}