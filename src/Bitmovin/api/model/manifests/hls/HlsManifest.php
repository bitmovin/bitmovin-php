<?php

namespace Bitmovin\api\model\manifests\hls;

use Bitmovin\api\model\manifests\AbstractManifest;
use JMS\Serializer\Annotation as JMS;

class HlsManifest extends AbstractManifest
{
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $hlsMediaPlaylistVersion;
    /**
     * @JMS\Type("integer")
     * @var  integer
     */
    private $hlsMasterPlaylistVersion;

    /**
     * @return integer
     */
    public function getHlsMediaPlaylistVersion()
    {
        return $this->hlsMediaPlaylistVersion;
    }

    /**
     * @param integer $hlsMediaPlaylistVersion
     */
    public function setHlsMediaPlaylistVersion($hlsMediaPlaylistVersion)
    {
        $this->hlsMediaPlaylistVersion = $hlsMediaPlaylistVersion;
    }

    /**
     * @return integer
     */
    public function getHlsMasterPlaylistVersion()
    {
        return $this->hlsMasterPlaylistVersion;
    }

    /**
     * @param integer $hlsMasterPlaylistVersion
     */
    public function setHlsMasterPlaylistVersion($hlsMasterPlaylistVersion)
    {
        $this->hlsMasterPlaylistVersion = $hlsMasterPlaylistVersion;
    }

}