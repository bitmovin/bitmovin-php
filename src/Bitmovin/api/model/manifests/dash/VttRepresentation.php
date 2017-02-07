<?php

namespace Bitmovin\api\model\manifests\dash;

use Bitmovin\api\model\AbstractModel;
use JMS\Serializer\Annotation as JMS;

class VttRepresentation extends AbstractModel
{
    /**
     * @JMS\type("string")
     * @var string
     */
    private $vttUrl;

    /**
     * @return string
     */
    public function getVttUrl()
    {
        return $this->vttUrl;
    }

    /**
     * @param string $vttUrl
     */
    public function setVttUrl($vttUrl)
    {
        $this->vttUrl = $vttUrl;
    }


}