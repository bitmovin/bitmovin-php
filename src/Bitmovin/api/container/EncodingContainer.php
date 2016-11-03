<?php

namespace Bitmovin\api\container;

use Bitmovin\api\enum\Status;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\inputs\Input;
use Bitmovin\input\AbstractInput;
use Bitmovin\input\FtpInput;
use Bitmovin\input\HttpInput;

class EncodingContainer
{

    /**
     * @var Input
     */
    public $apiInput;

    /**
     * @var AbstractInput
     */
    public $input;

    /**
     * @var CodecConfigContainer[]
     */
    public $codecConfigContainer = array();

    /**
     * @var Encoding
     */
    public $encoding;

    /**
     * @var string
     */
    public $status = Status::CREATED;


    /**
     * InputContainer constructor.
     * @param Input         $apiInput
     * @param AbstractInput $input
     */
    public function __construct(Input $apiInput, AbstractInput $input)
    {
        $this->apiInput = $apiInput;
        $this->input = $input;
    }

    public function getInputPath()
    {
        if ($this->input instanceof HttpInput || $this->input instanceof FtpInput)
        {
            $url = parse_url($this->input->url);
            $path = '';
            if (key_exists('path', $url))
            {
                $path .= $url['path'];
            }
            if (key_exists('query', $url))
            {
                $path .= '?' . $url['query'];
            }
            if (key_exists('fragment', $url))
            {
                $path .= $url['fragment'];
            }
            return $path;
        }
        throw new \InvalidArgumentException();
    }

}