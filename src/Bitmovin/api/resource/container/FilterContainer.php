<?php


namespace Bitmovin\api\resource\container;


use Bitmovin\api\resource\filters\WatermarkFilterResource;

class FilterContainer
{
    private $watermark;
    private $deinterlace;

    /**
     * InputContainer constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->watermark = new WatermarkFilterResource($apiKey);
        $this->deinterlace = new DeinterlaceFilterResource($apiKey);
    }

    /**
     * @return WatermarkFilterResource
     */
    public function watermark()
    {
        return $this->watermark;
    }

    /**
     * @return DeinterlaceFilterResource
     */
    public function deinterlace()
    {
        return $this->deinterlace;
    }

}