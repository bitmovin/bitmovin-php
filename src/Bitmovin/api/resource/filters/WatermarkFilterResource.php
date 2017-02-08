<?php

namespace Bitmovin\api\resource\filters;

use Bitmovin\api\model\filters\WatermarkFilter;
use Bitmovin\api\resource\AbstractResource;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class WatermarkFilterResource extends AbstractResource
{
    const LIST_NAME = 'items';

    /**
     * SpriteResource constructor.
     * @param string   $apiKey
     */
    public function __construct($apiKey)
    {
        $baseUri = RouteHelper::buildURI(ApiUrls::FILTERS_WATERMARK, array());

        parent::__construct($baseUri, WatermarkFilter::class, static::LIST_NAME, $apiKey);
    }

    /**
     * @param WatermarkFilter $filter
     *
     * @return WatermarkFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(WatermarkFilter $filter)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->createResource($filter);
    }

    /**
     * @param WatermarkFilter $filter
     *
     * @return WatermarkFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(WatermarkFilter $filter)
    {
        return $this->deleteById($filter->getId());
    }

    /**
     * @param WatermarkFilter $filter
     *
     * @return WatermarkFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(WatermarkFilter $filter)
    {
        return $this->getById($filter->getId());
    }

    /**
     * @param $filterId
     *
     * @return WatermarkFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($filterId)
    {
        /** @var WatermarkFilter $filter */
        $filter = $this->getResource($filterId);

        return $filter;
    }

    /**
     * @param $filterId
     *
     * @return WatermarkFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($filterId)
    {
        /** @var WatermarkFilter $filter */
        $filter = $this->deleteResource($filterId);

        return $filter;
    }
}