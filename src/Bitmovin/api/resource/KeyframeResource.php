<?php

namespace Bitmovin\api\resource;

use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\keyframes\Keyframe;
use Bitmovin\api\resource\AbstractResource;
use Bitmovin\api\util\ApiUrls;
use Bitmovin\api\util\RouteHelper;

class KeyframeResource extends AbstractResource
{
    const LIST_NAME = 'items';

    /** @var  Encoding */
    private $encoding;

    /**
     * KeyframeResource constructor.
     * @param Encoding $encoding
     * @param string   $apiKey
     */
    public function __construct(Encoding $encoding, $apiKey)
    {
        $this->encoding = $encoding;

        $baseUri = RouteHelper::buildURI(ApiUrls::ENCODING_KEYFRAMES, array(
            ApiUrls::PH_ENCODING_ID => $encoding->getId(),
        ));

        parent::__construct($baseUri, Keyframe::class, static::LIST_NAME, $apiKey);
    }

    /**
     * @param Keyframe $keyframe
     *
     * @return Keyframe
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(Keyframe $keyframe)
    {
        return $this->createResource($keyframe);
    }

    /**
     * @param Keyframe $keyframe
     *
     * @return Keyframe
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(Keyframe $keyframe)
    {
        return $this->deleteById($keyframe->getId());
    }

    /**
     * @param Keyframe $keyframe
     *
     * @return Keyframe
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(Keyframe $keyframe)
    {
        return $this->getById($keyframe->getId());
    }

    /**
     * @param $keyframeId
     *
     * @return Keyframe
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($keyframeId)
    {
        /** @var Keyframe $keyframe */
        $keyframe = $this->getResource($keyframeId);

        return $keyframe;
    }

    /**
     * @param $keyframeId
     *
     * @return Keyframe
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($keyframeId)
    {
        /** @var Keyframe $keyframe */
        $keyframe = $this->deleteResource($keyframeId);

        return $keyframe;
    }
}
