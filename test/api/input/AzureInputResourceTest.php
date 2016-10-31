<?php


namespace Bitmovin\test\api\input;


use Bitmovin\api\model\inputs\AzureInput;
use Bitmovin\api\model\inputs\Input;

class AzureInputResourceTest extends AbstractInputResourceTest
{

    /**
     * @return Input
     */
    protected function createInput()
    {
        $input = new AzureInput("test", "123");
        return $input;
    }

    /**
     * @return mixed
     */
    protected function getResource()
    {
        return $this->apiClient->inputs()->azure();
    }

    /**
     * @return string
     */
    protected function expectedClass()
    {
        return AzureInput::class;
    }
}