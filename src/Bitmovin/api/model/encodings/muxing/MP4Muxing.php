<?php

namespace Bitmovin\api\model\encodings\muxing;

use JMS\Serializer\Annotation as JMS;

class MP4Muxing extends AbstractMuxing
{
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $name;

    /**
     * MP4Muxing constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

}