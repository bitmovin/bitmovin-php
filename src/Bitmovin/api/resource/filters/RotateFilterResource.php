<?php

namespace Bitmovin\api\resource\filters;

use Bitmovin\api\model\filters\RotateFilter;
use Bitmovin\api\resource\AbstractResource;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class RotateFilterResource extends AbstractResource
{
    const LIST_NAME = 'items';

    /**
     * SpriteResource constructor.
     * @param string   $apiKey
     */
    public function __construct($apiKey)
    {
        $baseUri = RouteHelper::buildURI(ApiUrls::FILTERS_ROTATE, array());

        parent::__construct($baseUri, RotateFilter::class, static::LIST_NAME, $apiKey);
    }

    /**
     * @param RotateFilter $filter
     *
     * @return RotateFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(RotateFilter $filter)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->createResource($filter);
    }

    /**
     * @param RotateFilter $filter
     *
     * @return RotateFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(RotateFilter $filter)
    {
        return $this->deleteById($filter->getId());
    }

    /**
     * @param RotateFilter $filter
     *
     * @return RotateFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(RotateFilter $filter)
    {
        return $this->getById($filter->getId());
    }

    /**
     * @param $filterId
     *
     * @return RotateFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($filterId)
    {
        /** @var RotateFilter $filter */
        $filter = $this->getResource($filterId);

        return $filter;
    }

    /**
     * @param $filterId
     *
     * @return RotateFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($filterId)
    {
        /** @var RotateFilter $filter */
        $filter = $this->deleteResource($filterId);

        return $filter;
    }
}