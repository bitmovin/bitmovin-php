<?php

namespace Bitmovin\api\resource\encodings\streams\muxings\drm;

use Bitmovin\api\model\encodings\drms\AbstractDrm;
use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\muxing\AbstractMuxing;
use Bitmovin\api\resource\AbstractResource;

abstract class DrmResource extends AbstractResource
{
    const LIST_NAME = 'drm';

    /** @var  Encoding */
    protected $encoding;
    /** @var  AbstractMuxing */
    protected $muxing;

    /**
     * MuxingResource constructor.
     *
     * @param Encoding       $encoding
     * @param AbstractMuxing $muxing
     * @param string         $baseUri
     * @param string         $modelClassName
     * @param string         $apiKey
     */
    public function __construct(Encoding $encoding,  AbstractMuxing $muxing, $baseUri, $modelClassName, $apiKey)
    {
        parent::__construct($baseUri, $modelClassName, static::LIST_NAME, $apiKey);
        $this->encoding = $encoding;
        $this->muxing = $muxing;
    }

    /**
     * @param AbstractDrm $muxing
     *
     * @return AbstractDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function createDrm(AbstractDrm $muxing)
    {
        return $this->createResource($muxing);
    }

    /**
     * @param AbstractDrm $muxing
     *
     * @return AbstractDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */


    protected function deleteDrm(AbstractDrm $muxing)
    {
        return $this->deleteDrmById($muxing->getId());
    }

    /**
     * @param AbstractDrm $muxing
     *
     * @return AbstractDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function getDrms(AbstractDrm $muxing)
    {
        return $this->getDrmById($muxing->getId());

    }

    /**
     * @return AbstractDrm[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function listAllDrms()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResource();
    }

    /**
     * @param string $muxingId
     *
     * @return AbstractDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function getDrmById($muxingId)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getResource($muxingId);
    }

    /**
     * @param $muxingId
     *
     * @return AbstractDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    protected function deleteDrmById($muxingId)
    {
        return $this->deleteResource($muxingId);
    }
}