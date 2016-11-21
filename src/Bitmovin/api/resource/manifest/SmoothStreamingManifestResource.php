<?php

namespace Bitmovin\api\resource\manifest;

use Bitmovin\api\model\manifests\smoothstreaming\SmoothStreamingContentProtection;
use Bitmovin\api\model\manifests\smoothstreaming\SmoothStreamingManifest;
use Bitmovin\api\model\manifests\smoothstreaming\SmoothStreamingRepresentation;
use Bitmovin\api\model\Status;
use Bitmovin\api\resource\AbstractResource;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class SmoothStreamingManifestResource extends AbstractResource
{
    const LIST_NAME = 'manifests';

    /**
     * InputResource constructor.
     *
     * @param string $baseUri
     * @param string $modelClassName
     * @param string $apiKey
     */
    public function __construct($baseUri, $modelClassName, $apiKey)
    {
        parent::__construct($baseUri, $modelClassName, static::LIST_NAME, $apiKey);
    }

    /**
     * @param SmoothStreamingManifest $manifest
     * @return \Bitmovin\api\model\Status
     */
    public function status(SmoothStreamingManifest $manifest)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_SMOOTH_STATUS, $routeReplacementMap);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::getResourceCustomClass($baseUriEncoding, Status::class);
    }

    /**
     * @param SmoothStreamingManifest $manifest
     * @param SmoothStreamingRepresentation   $representation
     * @return SmoothStreamingRepresentation
     */
    public function createRepresentation(SmoothStreamingManifest $manifest, SmoothStreamingRepresentation $representation)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_SMOOTH_REPRESENTATION, $routeReplacementMap);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::postResource($representation, $baseUriEncoding, SmoothStreamingRepresentation::class);
    }

    /**
     * @param SmoothStreamingManifest          $manifest
     * @param SmoothStreamingContentProtection $contentProtection
     * @return SmoothStreamingRepresentation
     * @internal param SmoothStreamingRepresentation $representation
     */
    public function addContentProtection(SmoothStreamingManifest $manifest, SmoothStreamingContentProtection $contentProtection)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_SMOOTH_CONTENT_PROTECTION, $routeReplacementMap);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::postResource($contentProtection, $baseUriEncoding, SmoothStreamingContentProtection::class);
    }

    public function start(SmoothStreamingManifest $manifest)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_SMOOTH_START, $routeReplacementMap);

        return parent::postRequest($baseUriEncoding, "");
    }


    public function stop(SmoothStreamingManifest $manifest)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_SMOOTH_STOP, $routeReplacementMap);

        return parent::postRequest($baseUriEncoding, "");
    }


    public function restart(SmoothStreamingManifest $manifest)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_SMOOTH_RESTART, $routeReplacementMap);

        return parent::postRequest($baseUriEncoding, "");
    }

    /**
     * @param SmoothStreamingManifest $manifest
     *
     * @return SmoothStreamingManifest
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(SmoothStreamingManifest $manifest)
    {
        return parent::createResource($manifest);
    }

    /**
     * @param SmoothStreamingManifest $manifest
     *
     * @return SmoothStreamingManifest
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(SmoothStreamingManifest $manifest)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::deleteResource($manifest->getId());
    }

    /**
     * @param SmoothStreamingManifest $manifest
     *
     * @return SmoothStreamingManifest
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(SmoothStreamingManifest $manifest)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::getResource($manifest->getId());
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return SmoothStreamingManifest[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param string $manifestId
     *
     * @return SmoothStreamingManifest
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($manifestId)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::getResource($manifestId);
    }

    /**
     * @param string $manifestId
     *
     * @return SmoothStreamingManifest
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($manifestId)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::deleteResource($manifestId);
    }

}