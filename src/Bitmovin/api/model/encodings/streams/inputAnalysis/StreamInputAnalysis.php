<?php

namespace Bitmovin\api\model\encodings\streams\inputAnalysis;

use JMS\Serializer\Annotation as JMS;

class StreamInputAnalysis
{
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $inputId;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $inputPath;

    /**
     * @JMS\Type("Bitmovin\api\model\encodings\streams\inputAnalysis\StreamInputAnalysisDetails")
     * @var StreamInputAnalysisDetails
     */
    private $details;

    /**
     * @return string
     */
    public function getInputId()
    {
        return $this->inputId;
    }

    /**
     * @param string $inputId
     */
    public function setInputId($inputId)
    {
        $this->inputId = $inputId;
    }

    /**
     * @return string
     */
    public function getInputPath()
    {
        return $this->inputPath;
    }

    /**
     * @param string $inputPath
     */
    public function setInputPath($inputPath)
    {
        $this->inputPath = $inputPath;
    }

    /**
     * @return StreamInputAnalysisDetails
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param StreamInputAnalysisDetails[] $details
     */
    public function setDetails($details)
    {
        $this->details = $details;
    }


}