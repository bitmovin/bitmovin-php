<?php

namespace Bitmovin\api\resource\transfers;

use Bitmovin\api\model\Status;
use Bitmovin\api\model\transfers\TransferEncoding;
use Bitmovin\api\resource\AbstractResource;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class TransferEncodingResource extends AbstractResource
{
    const LIST_NAME = 'items';

    public function __construct($baseUri, $modelClassName, $apiKey)
    {
        parent::__construct($baseUri, $modelClassName, static::LIST_NAME, $apiKey);
    }

    /**
     * @param TransferEncoding $transferEncoding
     *
     * @return TransferEncoding
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(TransferEncoding $transferEncoding)
    {
        return $this->createResource($transferEncoding);
    }

    /**
     * @param TransferEncoding $transferEncoding
     *
     * @return Status
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function status(TransferEncoding $transferEncoding) {
        $routeReplacementMap = array(ApiUrls::PH_TRANSFER_ID => $transferEncoding->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::ENCODING_TRANSFERS_ENCODING_STATUS, $routeReplacementMap);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getResourceCustomClass($baseUriEncoding, Status::class);
    }

    /**
     * @param TransferEncoding $transferEncoding
     *
     * @return TransferEncoding
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(TransferEncoding $transferEncoding)
    {
        return $this->deleteResource($transferEncoding);
    }

    /**
     * @param TransferEncoding $transferEncoding
     *
     * @return TransferEncoding
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(TransferEncoding $transferEncoding)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getResource($transferEncoding->getId());
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return TransferEncoding[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $transferId
     *
     * @return TransferEncoding
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($transferId)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::getResource($transferId);
    }

    /**
     * @param $transferId
     *
     * @return TransferEncoding
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($transferId)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::deleteResource($transferId);
    }
}