<?php


namespace Bitmovin\test\api\output;


use Bitmovin\api\model\outputs\Output;
use Bitmovin\api\model\outputs\S3Output;

class S3OutputResourceTest extends AbstractOutputResourceTest
{

    /**
     * @return Output
     */
    protected function createOutput()
    {
        $output = new S3Output("test", "S3S3ACCESSKEYTEST123", "secretkeyTest123secretKeyTest123TestTest");
        return $output;
    }

    /**
     * @return mixed
     */
    protected function getResource()
    {
        return $this->apiClient->outputs()->s3();
    }

    /**
     * @return string
     */
    protected function expectedClass()
    {
        return S3Output::class;
    }
}