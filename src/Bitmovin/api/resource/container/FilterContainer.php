<?php


namespace Bitmovin\api\resource\container;


use Bitmovin\api\resource\filters\WatermarkFilterResource;

class FilterContainer
{
    private $watermark;

    /**
     * InputContainer constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->watermark = new WatermarkFilterResource($apiKey);
    }

    /**
     * @return WatermarkFilterResource
     */
    public function watermark()
    {
        return $this->watermark;
    }

}