<?php

namespace Bitmovin\api\resource\transfers;

use Bitmovin\api\model\Status;
use Bitmovin\api\model\transfers\TransferManifest;
use Bitmovin\api\resource\AbstractResource;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class TransferManifestResource extends AbstractResource
{
    const LIST_NAME = 'items';

    public function __construct($baseUri, $modelClassName, $apiKey)
    {
        parent::__construct($baseUri, $modelClassName, static::LIST_NAME, $apiKey);
    }

    /**
     * @param TransferManifest $transferManifest
     *
     * @return TransferManifest
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(TransferManifest $transferManifest)
    {
        return $this->createResource($transferManifest);
    }

    /**
     * @param TransferManifest $transferManifest
     *
     * @return Status
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function status(TransferManifest $transferManifest) {
        $routeReplacementMap = array(ApiUrls::PH_TRANSFER_ID => $transferManifest->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::ENCODING_TRANSFERS_MANIFEST_STATUS, $routeReplacementMap);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getResourceCustomClass($baseUriEncoding, Status::class);
    }

    /**
     * @param TransferManifest $transferManifest
     *
     * @return TransferManifest
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(TransferManifest $transferManifest)
    {
        return $this->deleteResource($transferManifest);
    }

    /**
     * @param TransferManifest $transferManifest
     *
     * @return TransferManifest
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(TransferManifest $transferManifest)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getResource($transferManifest->getId());
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return TransferManifest[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $transferManifestId
     *
     * @return TransferManifest
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($transferManifestId)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::getResource($transferManifestId);
    }

    /**
     * @param $transferManifestId
     *
     * @return TransferManifest
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($transferManifestId)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::deleteResource($transferManifestId);
    }
}