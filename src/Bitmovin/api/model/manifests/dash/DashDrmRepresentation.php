<?php


namespace Bitmovin\api\model\manifests\dash;


use JMS\Serializer\Annotation as JMS;

class DashDrmRepresentation extends DashRepresentation
{
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $drmId;

    /**
     * @return string
     */
    public function getDrmId()
    {
        return $this->drmId;
    }

    /**
     * @param string $drmId
     */
    public function setDrmId($drmId)
    {
        $this->drmId = $drmId;
    }

}