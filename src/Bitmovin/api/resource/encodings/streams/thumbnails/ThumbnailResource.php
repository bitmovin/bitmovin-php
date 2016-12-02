<?php

namespace Bitmovin\api\resource\encodings\streams\thumbnails;

use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\encodings\streams\thumbnails\Thumbnail;
use Bitmovin\api\resource\AbstractResource;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class ThumbnailResource extends AbstractResource
{
    const LIST_NAME = 'items';

    /** @var  Stream */
    private $stream;
    /** @var  Encoding */
    private $encoding;

    public function __construct(Encoding $encoding, Stream $stream, $apiKey)
    {
        $this->stream = $stream;
        $this->encoding = $encoding;

        $baseUri = RouteHelper::buildURI(ApiUrls::ENCODING_STREAMS_THUMBNAILS, array(
            ApiUrls::PH_ENCODING_ID => $encoding->getId(),
            ApiUrls::PH_STREAM_ID   => $stream->getId()
        ));

        parent::__construct($baseUri, Stream::class, static::LIST_NAME, $apiKey);
    }

    /**
     * @param Thumbnail $thumbnail
     *
     * @return Thumbnail
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(Thumbnail $thumbnail)
    {
        return $this->createResource($thumbnail);
    }

    /**
     * @param Thumbnail $thumbnail
     *
     * @return Thumbnail
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(Thumbnail $thumbnail)
    {
        return $this->deleteById($thumbnail->getId());
    }

    /**
     * @param Thumbnail $thumbnail
     *
     * @return Thumbnail
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(Thumbnail $thumbnail)
    {
        return $this->getById($thumbnail->getId());
    }

    /**
     * @param $thumbnailId
     *
     * @return Thumbnail
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($thumbnailId)
    {
        /** @var Thumbnail $thumbnail */
        $thumbnail = $this->getResource($thumbnailId);

        return $thumbnail;
    }

    /**
     * @param $thumbnailId
     *
     * @return Thumbnail
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($thumbnailId)
    {
        /** @var Thumbnail $thumbnail */
        $thumbnail = $this->deleteResource($thumbnailId);

        return $thumbnail;
    }
}