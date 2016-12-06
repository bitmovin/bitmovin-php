<?php


namespace Bitmovin\api\container;


use Bitmovin\api\ApiClient;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\manifests\IManifest;
use Bitmovin\api\model\transfers\TransferManifest;

class ManifestContainer
{
    /**
     * @var ApiClient
     */
    private $apiClient;

    /**
     * @var Encoding
     */
    public $manifest;

    /**
     * @var  TransferManifest
     */
    public $transfer;

    /**
     * @var  string
     */
    public $status;

    /**
     * ManifestContainer constructor.
     * @param ApiClient $apiClient
     * @param IManifest $manifest
     */
    public function __construct(ApiClient $apiClient, IManifest $manifest)
    {
        $this->apiClient = $apiClient;
        $this->manifest = $manifest;
    }

    public function getTransferManifestOutputPath(TransferJobContainer $transferJobContainer)
    {
        return $this->combinePath($transferJobContainer->getOutputPath(), "manifests");
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