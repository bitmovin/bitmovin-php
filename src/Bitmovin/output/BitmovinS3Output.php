<?php


namespace Bitmovin\output;

class BitmovinS3Output extends AbstractOutput
{
    /**
     * @var string AwsRegion enum
     */
    public $cloudRegion;

    /**
     * BitmovinS3Output constructor.
     *
     * @param string $cloudRegion * @param string $cloudRegion Recommended: Use, CloudRegion::AWS_US_EAST_1 to select a
     *                            specific region
     */
    public function __construct($cloudRegion)
    {
        $this->cloudRegion = $cloudRegion;
    }

}