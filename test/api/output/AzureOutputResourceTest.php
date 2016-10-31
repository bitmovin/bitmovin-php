<?php


namespace Bitmovin\test\api\output;


use Bitmovin\api\model\outputs\AzureOutput;
use Bitmovin\api\model\outputs\Output;

class AzureOutputResourceTest extends AbstractOutputResourceTest
{

    /**
     * @return Output
     */
    protected function createOutput()
    {
        $output = new AzureOutput("123", "test");
        return $output;
    }

    /**
     * @return mixed
     */
    protected function getResource()
    {
        return $this->apiClient->outputs()->azure();
    }

    /**
     * @return string
     */
    protected function expectedClass()
    {
        return AzureOutput::class;
    }
}