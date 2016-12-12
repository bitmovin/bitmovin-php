<?php

namespace Bitmovin\api\resource\encodings\streams\thumbnails;

use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\streams\Stream;
use Bitmovin\api\model\encodings\streams\thumbnails\Sprite;
use Bitmovin\api\resource\AbstractResource;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class SpriteResource extends AbstractResource
{
    const LIST_NAME = 'items';

    /** @var  Stream */
    private $stream;
    /** @var  Encoding */
    private $encoding;

    /**
     * SpriteResource constructor.
     * @param Encoding $encoding
     * @param Stream   $stream
     * @param string   $apiKey
     */
    public function __construct(Encoding $encoding, Stream $stream, $apiKey)
    {
        $this->stream = $stream;
        $this->encoding = $encoding;

        $baseUri = RouteHelper::buildURI(ApiUrls::ENCODING_STREAMS_SPRITES, array(
            ApiUrls::PH_ENCODING_ID => $encoding->getId(),
            ApiUrls::PH_STREAM_ID   => $stream->getId()
        ));

        parent::__construct($baseUri, Stream::class, static::LIST_NAME, $apiKey);
    }

    /**
     * @param Sprite $sprite
     *
     * @return Sprite
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(Sprite $sprite)
    {
        return $this->createResource($sprite);
    }

    /**
     * @param Sprite $sprite
     *
     * @return Sprite
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(Sprite $sprite)
    {
        return $this->deleteById($sprite->getId());
    }

    /**
     * @param Sprite $sprite
     *
     * @return Sprite
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(Sprite $sprite)
    {
        return $this->getById($sprite->getId());
    }

    /**
     * @param $spriteId
     *
     * @return Sprite
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($spriteId)
    {
        /** @var Sprite $sprite */
        $sprite = $this->getResource($spriteId);

        return $sprite;
    }

    /**
     * @param $spriteId
     *
     * @return Sprite
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($spriteId)
    {
        /** @var Sprite $sprite */
        $sprite = $this->deleteResource($spriteId);

        return $sprite;
    }
}