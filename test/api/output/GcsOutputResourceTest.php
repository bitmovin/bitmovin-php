<?php


namespace Bitmovin\test\api\output;


use Bitmovin\api\model\outputs\GcsOutput;
use Bitmovin\api\model\outputs\Output;

class GcsOutputResourceTest extends AbstractOutputResourceTest
{

    /**
     * @return Output
     */
    protected function createOutput()
    {
        $output = new GcsOutput("test", "GOOGACCESSKEYTEST123", "secretkeyTest123secretKeyTest123TestTest");
        return $output;
    }

    /**
     * @return mixed
     */
    protected function getResource()
    {
        return $this->apiClient->outputs()->gcs();
    }

    /**
     * @return string
     */
    protected function expectedClass()
    {
        return GcsOutput::class;
    }
}