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

    /**
     * @param \Bitmovin\output\S3Output $output
     * @return S3Output
     */
    public static function createFromS3Output(\Bitmovin\output\S3Output $output)
    {
        $convertedOutput = new S3Output($output->bucket, $output->accessKey, $output->secretKey);
        $convertedOutput->setCloudRegion($output->cloudRegion);
        return $convertedOutput;
    }

    /**
     * @param \Bitmovin\output\FtpOutput $output
     * @return FtpOutput
     */
    public static function createFromFtpOutput(\Bitmovin\output\FtpOutput $output)
    {
        $convertedOutput = new FtpOutput($output->host, $output->username, $output->password);
        $convertedOutput->setPassive($output->passive);
        $convertedOutput->setPort($output->port);
        return $convertedOutput;
    }

}