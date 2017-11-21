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

    public function getId()
    {
        return null;
    }

}