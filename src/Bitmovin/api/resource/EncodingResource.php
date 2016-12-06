<?php

namespace Bitmovin\api\resource;

use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\helper\LiveEncodingDetails;
use Bitmovin\api\model\Status;
use Bitmovin\api\resource\encodings\streams\muxings\MuxingContainer;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class EncodingResource extends AbstractResource
{
    const LIST_NAME = "items";

    /**
     * EncodingResource constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        parent::__construct(ApiUrls::ENCODINGS, Encoding::class, static::LIST_NAME, $apiKey);
    }

    /**
     * @param Encoding $encoding
     * @return StreamResource
     */
    public function streams(Encoding $encoding)
    {
        return new StreamResource($encoding, parent::getApiKey());
    }

    /**
     * @param Encoding $encoding
     *
     * @return MuxingContainer
     */
    public function muxings(Encoding $encoding)
    {
        return new MuxingContainer($encoding, parent::getApiKey());
    }

    /**
     * @param Encoding $encoding
     */
    public function start(Encoding $encoding)
    {
        $routeReplacementMap = array(ApiUrls::PH_ENCODING_ID => $encoding->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::ENCODING_START, $routeReplacementMap);

        parent::postRequest($baseUriEncoding, "");
    }

    /**
     * @param Encoding $encoding
     * @return Status
     */
    public function status(Encoding $encoding)
    {
        $routeReplacementMap = array(ApiUrls::PH_ENCODING_ID => $encoding->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::ENCODING_STATUS, $routeReplacementMap);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::getResourceCustomClass($baseUriEncoding, Status::class);
    }

    /**
     * @param Encoding $encoding
     */
    public function stop(Encoding $encoding)
    {
        $routeReplacementMap = array(ApiUrls::PH_ENCODING_ID => $encoding->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::ENCODING_STOP, $routeReplacementMap);

        parent::postRequest($baseUriEncoding, "");
    }

    /***
     * @param Encoding $encoding
     * @param string   $streamKey
     */
    public function startLivestream(Encoding $encoding, $streamKey)
    {
        $routeReplacementMap = array(ApiUrls::PH_ENCODING_ID => $encoding->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::ENCODING_START_LIVE, $routeReplacementMap);

        parent::postRequest($baseUriEncoding, json_encode(array("streamKey" => $streamKey)));
    }

    /**
     * @param Encoding $encoding
     */
    public function stopLivestream(Encoding $encoding)
    {
        $routeReplacementMap = array(ApiUrls::PH_ENCODING_ID => $encoding->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::ENCODING_STOP_LIVE, $routeReplacementMap);

        parent::buildResourceFromResponse(parent::postRequest($baseUriEncoding, ""));
    }

    /**
     * @param Encoding $encoding
     * @return LiveEncodingDetails
     */
    public function getLivestreamDetails(Encoding $encoding)
    {
        $routeReplacementMap = array(ApiUrls::PH_ENCODING_ID => $encoding->getId());
        $baseUriEncoding = RouteHelper::buildURI(ApiUrls::ENCODING_DETAILS_LIVE, $routeReplacementMap);

        return parent::getResourceObject($baseUriEncoding, LiveEncodingDetails::class);
    }

    /**
     * @param Encoding $encoding
     *
     * @return Encoding
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(Encoding $encoding)
    {
        return parent::createResource($encoding);
    }

    /**
     * @param Encoding $encoding
     *
     * @return Encoding
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(Encoding $encoding)
    {
        return $this->deleteById($encoding->getId());
    }

    /**
     * @param Encoding $encoding
     *
     * @return Encoding
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(Encoding $encoding)
    {
        return $this->getById($encoding->getId());
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return \Bitmovin\api\model\encodings\Encoding[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $encodingId
     *
     * @return Encoding
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($encodingId)
    {
        /** @var Encoding $encoding */
        $encoding = $this->getResource($encodingId);

        return $encoding;
    }

    /**
     * @param $encodingId
     *
     * @return Encoding
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($encodingId)
    {
        /** @var Encoding $encoding */
        $encoding = $this->deleteResource($encodingId);

        return $encoding;
    }
}