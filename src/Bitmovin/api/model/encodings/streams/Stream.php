<?php

namespace Bitmovin\api\model\encodings\streams;

use Bitmovin\api\model\AbstractModel;
use Bitmovin\api\model\codecConfigurations\CodecConfiguration;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use Bitmovin\api\model\encodings\helper\InputStream;
use JMS\Serializer\Annotation as JMS;

class Stream extends AbstractModel
{
    /**
     * @JMS\Type("array<Bitmovin\api\model\encodings\helper\InputStream>")
     * @var InputStream[]
     */
    private $inputStreams;
    /**
     * @JMS\Type("string")
     * @var  string UUID
     */
    private $codecConfigId;

    /**
     * @JMS\Type("array<Bitmovin\api\model\encodings\helper\EncodingOutput>")
     * @var  EncodingOutput[]
     */
    private $outputs;

    /**
     * Stream constructor.
     *
     * @param CodecConfiguration $codecConfiguration
     * @param array              $inputStreams
     */
    public function __construct(CodecConfiguration $codecConfiguration, array $inputStreams)
    {
        $this->inputStreams = $inputStreams;
        $this->codecConfigId = $codecConfiguration->getId();
    }

    /**
     * @return \Bitmovin\api\model\encodings\helper\InputStream[]
     */
    public function getInputStreams()
    {
        return $this->inputStreams;
    }

    /**
     * @param \Bitmovin\api\model\encodings\helper\InputStream[] $inputStreams
     */
    public function setInputStreams($inputStreams)
    {
        $this->inputStreams = $inputStreams;
    }

    /**
     * @return string
     */
    public function getCodecConfigId()
    {
        return $this->codecConfigId;
    }

    /**
     * @param string $codecConfigId
     */
    public function setCodecConfigId($codecConfigId)
    {
        $this->codecConfigId = $codecConfigId;
    }

    /**
     * @return \Bitmovin\api\model\encodings\helper\EncodingOutput[]
     */
    public function getOutputs()
    {
        return $this->outputs;
    }

    /**
     * @param \Bitmovin\api\model\encodings\helper\EncodingOutput[] $outputs
     */
    public function setOutputs($outputs)
    {
        $this->outputs = $outputs;
    }

}