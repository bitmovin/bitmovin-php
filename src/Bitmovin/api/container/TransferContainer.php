<?php


namespace Bitmovin\api\container;


use Bitmovin\api\ApiClient;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\transfers\TransferEncoding;

class TransferContainer
{
    /**
     * @var ApiClient
     */
    private $apiClient;

    /**
     * @var Encoding
     */
    public $encoding;

    /**
     * @var  TransferEncoding
     */
    public $transfer;

    /**
     * TransferContainer constructor.
     *
     * @param ApiClient $apiClient
     * @param Encoding  $encoding
     */
    public function __construct(ApiClient $apiClient, Encoding $encoding)
    {
        $this->apiClient = $apiClient;
        $this->encoding = $encoding;
    }

    public function getTransferOutputPath(TransferJobContainer $transferJobContainer)
    {
        return $this->combinePath($transferJobContainer->getOutputPath(), $this->encoding->getId());
    }

    /**
     * @param string[] ...$paths
     * @return string
     */
    private function combinePath(...$paths)
    {
        $path = '';
        foreach ($paths as $item)
        {
            if (substr($item, 0, 1) != '/' && substr($path, -1) != '/')
            {
                $path .= '/';
            }
            $path .= $item;
        }
        return $path;
    }
}