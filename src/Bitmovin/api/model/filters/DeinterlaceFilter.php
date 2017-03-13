<?php

namespace Bitmovin\api\model\filters;

use JMS\Serializer\Annotation as JMS;

class DeinterlaceFilter extends AbstractFilter
{

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $mode;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $parity;

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return string
     */
    public function getParity()
    {
        return $this->parity;
    }

    /**
     * @param string $parity
     */
    public function setParity($parity)
    {
        $this->parity = $parity;
    }

}