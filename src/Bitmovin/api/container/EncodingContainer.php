<?php

namespace Bitmovin\api\container;

use Bitmovin\api\ApiClient;
use Bitmovin\api\enum\Status;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\inputs\Input;
use Bitmovin\input\AbstractInput;
use Bitmovin\input\FtpInput;
use Bitmovin\input\HttpInput;
use Bitmovin\input\RtmpInput;

class EncodingContainer
{

    /**
     * @var ApiClient
     */
    private $apiClient;

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
     * @param ApiClient     $apiClient
     * @param Input         $apiInput
     * @param AbstractInput $input
     */
    public function __construct(ApiClient $apiClient, Input $apiInput, AbstractInput $input)
    {
        $this->apiClient = $apiClient;
        $this->apiInput = $apiInput;
        $this->input = $input;
    }

    public function deleteEncoding()
    {
        if (!$this->encoding instanceof Encoding || $this->encoding->getId() == null)
            return;
        $this->apiClient->encodings()->delete($this->encoding);
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
        if ($this->input instanceof RtmpInput)
        {
            return 'live';
        }
        throw new \InvalidArgumentException();
    }

}