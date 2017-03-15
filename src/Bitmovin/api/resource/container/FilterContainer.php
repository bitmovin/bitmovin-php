<?php


namespace Bitmovin\api\resource\container;


use Bitmovin\api\resource\filters\WatermarkFilterResource;
use Bitmovin\api\resource\filters\DeinterlaceFilterResource;
use Bitmovin\api\resource\filters\CropFilterResource;
use Bitmovin\api\resource\filters\RotateFilterResource;

class FilterContainer
{
    private $watermark;
    private $deinterlace;
    private $crop;
    private $rotate;

    /**
     * InputContainer constructor.
     *
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->watermark = new WatermarkFilterResource($apiKey);
        $this->deinterlace = new DeinterlaceFilterResource($apiKey);
        $this->crop = new CropFilterResource($apiKey);
        $this->rotate = new RotateFilterResource($apiKey);
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

    /**
     * @return CropFilterResource
     */
    public function crop()
    {
        return $this->crop;
    }

    /**
     * @return RotateFilterResource
     */
    public function rotate()
    {
        return $this->rotate;
    }

}