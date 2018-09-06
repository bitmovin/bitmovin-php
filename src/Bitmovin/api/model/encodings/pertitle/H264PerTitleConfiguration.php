<?php

namespace Bitmovin\api\model\encodings\pertitle;

use JMS\Serializer\Annotation as JMS;

class H264PerTitleConfiguration extends PerTitleConfiguration
{
    /** @JMS\Type("float") @var float $targetQualityCrf*/
    private $targetQualityCrf;

    /**
     * @return float
     */
    public function getTargetQualityCrf()
    {
        return $this->targetQualityCrf;
    }

    /**
     * @param float $targetQualityCrf
     */
    public function setTargetQualityCrf($targetQualityCrf)
    {
        $this->targetQualityCrf = $targetQualityCrf;
    }
}
