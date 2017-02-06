<?php

namespace Bitmovin\api\resource\manifest;


use Bitmovin\api\model\manifests\hls\HlsManifest;
use Bitmovin\api\model\manifests\hls\MediaInfo;
use Bitmovin\api\model\manifests\hls\StreamInfo;
use Bitmovin\api\model\manifests\hls\VttMedia;
use Bitmovin\api\model\Status;
use Bitmovin\api\resource\AbstractResource;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class HlsManifestResource extends AbstractResource
{
    const LIST_NAME = 'items';

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
     * @param HlsManifest $manifest
     * @return \Bitmovin\api\model\Status
     */
    public function status(HlsManifest $manifest)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_HLS_STATUS, $routeReplacementMap);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::getResourceCustomClass($baseUriEncoding, Status::class);
    }

    /**
     * @param HlsManifest $manifest
     * @param MediaInfo   $mediaInfo
     * @return MediaInfo
     */
    public function createMediaInfo(HlsManifest $manifest, MediaInfo $mediaInfo)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_HLS_MEDIA, $routeReplacementMap);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::postResource($mediaInfo, $baseUriEncoding, MediaInfo::class);
    }

    /**
     * @param HlsManifest $manifest
     * @param StreamInfo  $streamInfo
     * @return StreamInfo
     */
    public function createStreamInfo(HlsManifest $manifest, StreamInfo $streamInfo)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_HLS_STREAMS, $routeReplacementMap);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::postResource($streamInfo, $baseUriEncoding, StreamInfo::class);
    }

    /**
     * @param HlsManifest $manifest
     * @param VttMedia $vttMedia
     * @return VttMedia
     */
    public function addVttMedia(HlsManifest $manifest, VttMedia $vttMedia)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId());
        $vttMediaBaseUri = RouteHelper::buildURI(ApiUrls::MANIFEST_HLS_VTT_MEDIA, $routeReplacementMap);

        return parent::postResource($vttMedia, $vttMediaBaseUri, VttMedia::class);
    }


    public function start(HlsManifest $manifest)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_HLS_START, $routeReplacementMap);

        return parent::postRequest($baseUriEncoding, "");
    }


    public function stop(HlsManifest $manifest)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_HLS_STOP, $routeReplacementMap);

        return parent::postRequest($baseUriEncoding, "");
    }


    public function restart(HlsManifest $manifest)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_HLS_RESTART, $routeReplacementMap);

        return parent::postRequest($baseUriEncoding, "");
    }

    /**
     * @param HlsManifest $manifest
     *
     * @return HlsManifest
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(HlsManifest $manifest)
    {
        return parent::createResource($manifest);
    }

    /**
     * @param HlsManifest $manifest
     *
     * @return HlsManifest
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(HlsManifest $manifest)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::deleteResource($manifest->getId());
    }

    /**
     * @param HlsManifest $manifest
     *
     * @return HlsManifest
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(HlsManifest $manifest)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::getResource($manifest->getId());
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return HlsManifest[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param string $manifestId
     *
     * @return HlsManifest
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
     * @return HlsManifest
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($manifestId)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::deleteResource($manifestId);
    }

}