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

}