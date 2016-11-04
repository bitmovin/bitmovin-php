<?php

namespace Bitmovin\api\resource\manifest;

use Bitmovin\api\model\manifests\dash\AdaptationSet;
use Bitmovin\api\model\manifests\dash\AudioAdaptationSet;
use Bitmovin\api\model\manifests\dash\ContentProtection;
use Bitmovin\api\model\manifests\dash\DashDrmRepresentation;
use Bitmovin\api\model\manifests\dash\DashManifest;
use Bitmovin\api\model\manifests\dash\DashRepresentation;
use Bitmovin\api\model\manifests\dash\Period;
use Bitmovin\api\model\manifests\dash\VideoAdaptationSet;
use Bitmovin\api\model\Status;
use Bitmovin\api\resource\AbstractResource;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class DashManifestResource extends AbstractResource
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
     * @param DashManifest $manifest
     * @param Period       $period
     * @return Period
     */
    public function createPeriod(DashManifest $manifest, Period $period)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_DASH_PERIODS, $routeReplacementMap);

        return parent::postResource($period, $baseUriEncoding, Period::class);
    }

    /**
     * @param DashManifest       $manifest
     * @param Period             $period
     * @param VideoAdaptationSet $set
     * @return VideoAdaptationSet
     */
    public function addVideoAdaptionSetToPeriod(DashManifest $manifest, Period $period, VideoAdaptationSet $set)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId(), ApiUrls::PH_PERIOD_ID => $period->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_DASH_PERIODS_VIDEO_ADAPTION_SET, $routeReplacementMap);

        return parent::postResource($set, $baseUriEncoding, VideoAdaptationSet::class);
    }

    /**
     * @param DashManifest       $manifest
     * @param Period             $period
     * @param AudioAdaptationSet $set
     * @return AudioAdaptationSet
     */
    public function addAudioAdaptionSetToPeriod(DashManifest $manifest, Period $period, AudioAdaptationSet $set)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId(), ApiUrls::PH_PERIOD_ID => $period->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_DASH_PERIODS_AUDIO_ADAPTION_SET, $routeReplacementMap);

        return parent::postResource($set, $baseUriEncoding, AudioAdaptationSet::class);
    }

    /**
     * @param DashManifest                     $manifest
     * @param Period                           $period
     * @param AdaptationSet $set
     * @param DashDrmRepresentation            $representation
     * @return DashDrmRepresentation
     */
    public function addDrmRepresentationToAdaptationSet(DashManifest $manifest, Period $period, AdaptationSet $set, DashDrmRepresentation $representation)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId(),
            ApiUrls::PH_PERIOD_ID => $period->getId(),
            ApiUrls::PH_ADAPTION_ID => $set->getId(),
        );
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_DASH_PERIODS_ADAPTION_SET_REPRESENTATION_FMP4, $routeReplacementMap);

        return parent::postResource($representation, $baseUriEncoding, DashDrmRepresentation::class);
    }

    /**
     * @param DashManifest      $manifest
     * @param Period            $period
     * @param AdaptationSet     $set
     * @param ContentProtection $contentProtection
     * @return DashDrmRepresentation
     */
    public function addContentProtectionToAdaptationSet(DashManifest $manifest, Period $period, AdaptationSet $set, ContentProtection $contentProtection)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId(),
            ApiUrls::PH_PERIOD_ID => $period->getId(),
            ApiUrls::PH_ADAPTION_ID => $set->getId(),
        );
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_DASH_PERIODS_ADAPTION_SET_CONTENT_PROTECTION, $routeReplacementMap);

        return parent::postResource($contentProtection, $baseUriEncoding, ContentProtection::class);
    }

    /**
     * @param DashManifest       $manifest
     * @param Period             $period
     * @param AdaptationSet      $set
     * @param DashRepresentation $representation
     * @param ContentProtection  $contentProtection
     * @return DashDrmRepresentation
     */
    public function addContentProtectionTofMP4Representation(DashManifest $manifest, Period $period, AdaptationSet $set, DashRepresentation $representation, ContentProtection $contentProtection)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId(),
            ApiUrls::PH_PERIOD_ID => $period->getId(),
            ApiUrls::PH_ADAPTION_ID => $set->getId(),
            ApiUrls::PH_REPRESENTATION_ID => $representation->getId()
        );
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_DASH_PERIODS_ADAPTION_SET_REPRESENTATION_FMP4_CONTENT_PROTECTION, $routeReplacementMap);

        return parent::postResource($contentProtection, $baseUriEncoding, ContentProtection::class);
    }

    /**
     * @param DashManifest                             $manifest
     * @param Period                                   $period
     * @param AdaptationSet                            $set
     * @param DashDrmRepresentation $representation
     * @param ContentProtection                        $contentProtection
     * @return DashDrmRepresentation
     */
    public function addContentProtectionToDRMfMP4Representation(DashManifest $manifest, Period $period, AdaptationSet $set, DashDrmRepresentation $representation, ContentProtection $contentProtection)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId(),
            ApiUrls::PH_PERIOD_ID => $period->getId(),
            ApiUrls::PH_ADAPTION_ID => $set->getId(),
            ApiUrls::PH_REPRESENTATION_ID => $representation->getId()
        );
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_DASH_PERIODS_ADAPTION_SET_REPRESENTATION_FMP4_DRM_CONTENT_PROTECTION, $routeReplacementMap);

        return parent::postResource($contentProtection, $baseUriEncoding, ContentProtection::class);
    }

    /**
     * @param DashManifest       $manifest
     * @param Period             $period
     * @param AdaptationSet      $set
     * @param DashRepresentation $representation
     * @return DashRepresentation
     */
    public function addRepresentationToAdaptationSet(DashManifest $manifest, Period $period, AdaptationSet $set, DashRepresentation $representation)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId(),
            ApiUrls::PH_PERIOD_ID => $period->getId(),
            ApiUrls::PH_ADAPTION_ID => $set->getId(),
        );
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_DASH_PERIODS_ADAPTION_SET_REPRESENTATION_FMP4, $routeReplacementMap);

        return parent::postResource($representation, $baseUriEncoding, DashDrmRepresentation::class);
    }


    /**
     * @param DashManifest $manifest
     * @return \Bitmovin\api\model\Status
     */
    public function status(DashManifest $manifest)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_DASH_STATUS, $routeReplacementMap);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::getResourceCustomClass($baseUriEncoding, Status::class);
    }


    public function start(DashManifest $manifest)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_DASH_START, $routeReplacementMap);

        return parent::postRequest($baseUriEncoding, "");
    }


    public function stop(DashManifest $manifest)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_DASH_STOP, $routeReplacementMap);

        return parent::postRequest($baseUriEncoding, "");
    }


    public function restart(DashManifest $manifest)
    {
        $routeReplacementMap = array(ApiUrls::PH_MANIFEST_ID => $manifest->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::MANIFEST_DASH_RESTART, $routeReplacementMap);

        return parent::postRequest($baseUriEncoding, "");
    }

    /**
     * @param DashManifest $manifest
     *
     * @return DashManifest
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(DashManifest $manifest)
    {
        return parent::createResource($manifest);
    }

    /**
     * @param DashManifest $manifest
     *
     * @return DashManifest
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(DashManifest $manifest)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::deleteResource($manifest->getId());
    }

    /**
     * @param DashManifest $manifest
     *
     * @return DashManifest
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(DashManifest $manifest)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::getResource($manifest->getId());
    }

    /**
     * @return DashManifest[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function listAll()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::listResource();
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return DashManifest[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param string $manifestId
     *
     * @return DashManifest
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
     * @return DashManifest
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($manifestId)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::deleteResource($manifestId);
    }

}