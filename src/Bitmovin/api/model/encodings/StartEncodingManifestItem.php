<?php

namespace Bitmovin\api\model\encodings;

use Bitmovin\api\model\ModelInterface;
use JMS\Serializer\Annotation as JMS;

class StartEncodingManifestItem implements ModelInterface
{

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $manifestId;

    /**
     * @return string
     */
    public function getManifestId()
    {
        return $this->$manifestId;
    }

    /**
     * @param string $manifestId
     */
    public function setManifestId($manifestId)
    {
        $this->manifestId = $manifestId;
    }

    public function getId()
    {
        return null;
    }

}