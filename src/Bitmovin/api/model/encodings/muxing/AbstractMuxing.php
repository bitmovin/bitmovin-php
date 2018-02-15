<?php


namespace Bitmovin\api\model\encodings\muxing;


use Bitmovin\api\model\AbstractModel;
use Bitmovin\api\model\encodings\helper\EncodingOutput;
use JMS\Serializer\Annotation as JMS;

abstract class AbstractMuxing extends AbstractModel
{
    /**
     * @JMS\Type("array<Bitmovin\api\model\encodings\helper\EncodingOutput>")
     * @var  EncodingOutput[]
     */
    private $outputs;

    /**
     * @JMS\Type("array<Bitmovin\api\model\encodings\muxing\MuxingStream>")
     * @var  MuxingStream[]
     */
    private $streams;

    /**
     * @JMS\Type("double")
     * @var  double
     */
    private $avgBitrate;

    /**
     * @JMS\Type("double")
     * @var  double
     */
    private $maxBitrate;

    /**
     * @JMS\Type("double")
     * @var  double
     */
    private $minBitrate;

    /**
     * @JMS\Type("string")
     * @var string StreamConditionsMode
     */
    private $streamConditionsMode;

    /**
     * @return float
     */
    public function getAvgBitrate()
    {
        return $this->avgBitrate;
    }

    /**
     * @return float
     */
    public function getMaxBitrate()
    {
        return $this->maxBitrate;
    }

    /**
     * @return float
     */
    public function getMinBitrate()
    {
        return $this->minBitrate;
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

    /**
     * @return MuxingStream[]
     */
    public function getStreams()
    {
        return $this->streams;
    }

    /**
     * @param MuxingStream[] $streams
     */
    public function setStreams($streams)
    {
        $this->streams = $streams;
    }

    /**
     * @return string
     */
    public function getStreamConditionsMode()
    {
        return $this->streamConditionsMode;
    }

    /**
     * @param string $streamConditionsMode
     */
    public function setStreamConditionsMode($streamConditionsMode)
    {
        $this->streamConditionsMode = $streamConditionsMode;
    }
}