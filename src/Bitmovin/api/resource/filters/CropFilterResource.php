<?php

namespace Bitmovin\api\resource\filters;

use Bitmovin\api\model\filters\CropFilter;
use Bitmovin\api\resource\AbstractResource;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class CropFilterResource extends AbstractResource
{
    const LIST_NAME = 'items';

    /**
     * SpriteResource constructor.
     * @param string   $apiKey
     */
    public function __construct($apiKey)
    {
        $baseUri = RouteHelper::buildURI(ApiUrls::FILTERS_CROP, array());

        parent::__construct($baseUri, CropFilter::class, static::LIST_NAME, $apiKey);
    }

    /**
     * @param CropFilter $filter
     *
     * @return CropFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(CropFilter $filter)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->createResource($filter);
    }

    /**
     * @param CropFilter $filter
     *
     * @return CropFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(CropFilter $filter)
    {
        return $this->deleteById($filter->getId());
    }

    /**
     * @param CropFilter $filter
     *
     * @return CropFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(CropFilter $filter)
    {
        return $this->getById($filter->getId());
    }

    /**
     * @param $filterId
     *
     * @return CropFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($filterId)
    {
        /** @var CropFilter $filter */
        $filter = $this->getResource($filterId);

        return $filter;
    }

    /**
     * @param $filterId
     *
     * @return CropFilter
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($filterId)
    {
        /** @var CropFilter $filter */
        $filter = $this->deleteResource($filterId);

        return $filter;
    }
}