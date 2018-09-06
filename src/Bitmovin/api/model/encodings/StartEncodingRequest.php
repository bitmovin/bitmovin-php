<?php

namespace Bitmovin\api\model\encodings;

use Bitmovin\api\model\encodings\pertitle\PerTitle;
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
     * @JMS\SerializedName("perTitle")
     * @JMS\Type("Bitmovin\api\model\encodings\pertitle\PerTitle")
     * @var PerTitle $perTitle
     */
    private $perTitle;

    /**
     * @JMS\SerializedName("encodingMode")
     * @JMS\Type("string")
     * @var EncodingMode $encodingMode
     */
    private $encodingMode;

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
     * @return PerTitle
     */
    public function getPerTitle()
    {
        return $this->perTitle;
    }

    /**
     * @param PerTitle $perTitle
     */
    public function setPerTitle($perTitle)
    {
        $this->perTitle = $perTitle;
    }

    /**
     * @return EncodingMode
     */
    public function getEncodingMode()
    {
        return $this->encodingMode;
    }

    /**
     * @param EncodingMode $encodingMode
     */
    public function setEncodingMode($encodingMode)
    {
        $this->encodingMode = $encodingMode;
    }

    public function getId()
    {
        return null;
    }

}