<?php


namespace Bitmovin\api\enum\codecConfigurations;

use Bitmovin\api\enum\AbstractEnum;
use JMS\Serializer\Annotation as JMS;


class CodecConfigType extends AbstractEnum
{
    const AAC = 'AAC';
    const H264 = 'H264';
    const H265 = 'H265';
    const VP9 = 'VP9';

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $type;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

}