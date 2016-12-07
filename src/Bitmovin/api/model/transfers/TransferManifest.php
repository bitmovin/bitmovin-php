<?php

namespace Bitmovin\api\model\transfers;

use Bitmovin\api\model\manifests\AbstractManifest;
use JMS\Serializer\Annotation as JMS;

class TransferManifest extends AbstractTransfer
{

    /**
     * @JMS\Type("string")
     * @var  string UUID
     */
    private $manifestId;

    /**
     * TransferManifest constructor.
     *
     * @param AbstractManifest $manifest
     */
    public function __construct(AbstractManifest $manifest)
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
}