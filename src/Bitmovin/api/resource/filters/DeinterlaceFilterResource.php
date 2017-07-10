<?php

namespace Bitmovin\api\resource\filters;

use Bitmovin\api\model\filters\DeinterlaceFilter;
use Bitmovin\api\resource\AbstractResource;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class DeinterlaceFilterResource extends AbstractResource
{
    const LIST_NAME = 'items';

    /**
     * SpriteResource constructor.
     * @param string   $apiKey
     */
    public function __construct($apiKey)
    {
        $baseUri = RouteHelper::buildURI(ApiUrls::FILTERS_DEINTERLACE, array());

        parent::__construct($baseUri, DeinterlaceFilter::class, static::LIST_NAME, $apiKey);
    }

    /**
     * @param DeinterlaceFilter $filter
     *
     * @return DeinterlaceFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(DeinterlaceFilter $filter)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->createResource($filter);
    }

    /**
     * @param DeinterlaceFilter $filter
     *
     * @return DeinterlaceFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(DeinterlaceFilter $filter)
    {
        return $this->deleteById($filter->getId());
    }

    /**
     * @param DeinterlaceFilter $filter
     *
     * @return DeinterlaceFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(DeinterlaceFilter $filter)
    {
        return $this->getById($filter->getId());
    }

    /**
     * @param $filterId
     *
     * @return DeinterlaceFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($filterId)
    {
        /** @var DeinterlaceFilter $filter */
        $filter = $this->getResource($filterId);

        return $filter;
    }

    /**
     * @param $filterId
     *
     * @return DeinterlaceFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($filterId)
    {
        /** @var DeinterlaceFilter $filter */
        $filter = $this->deleteResource($filterId);

        return $filter;
    }
}