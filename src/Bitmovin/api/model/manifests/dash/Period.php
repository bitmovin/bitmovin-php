<?php


namespace Bitmovin\api\model\manifests\dash;


use Bitmovin\api\model\AbstractModel;
use JMS\Serializer\Annotation as JMS;

class Period extends AbstractModel
{
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $start;
    /**
     * @JMS\Type("string")
     * @var  string
     */
    private $duration;

    /**
     * @return string
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param string $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return string
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param string $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

}