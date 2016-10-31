<?php


namespace Bitmovin\api\model\outputs;

class OutputConverterFactory
{

    /**
     * @param \Bitmovin\output\GcsOutput $output
     * @return GcsOutput
     */
    public static function createFromGcsOutput(\Bitmovin\output\GcsOutput $output)
    {
        $convertedOutput = new GcsOutput($output->bucket, $output->accessKey, $output->secretKey);
        $convertedOutput->setCloudRegion($output->cloudRegion);
        return $convertedOutput;
    }

}