<?php


namespace Bitmovin\test\api\input;


use Bitmovin\api\model\inputs\AsperaInput;
use Bitmovin\api\model\inputs\Input;

class AsperaInputResourceTest extends AbstractInputResourceTest
{

    /**
     * @return Input
     */
    protected function createInput()
    {
        $input = new AsperaInput("test", "123", "234");
        return $input;
    }

    /**
     * @return mixed
     */
    protected function getResource()
    {
        return $this->apiClient->inputs()->aspera();
    }

    /**
     * @return string
     */
    protected function expectedClass()
    {
        return AsperaInput::class;
    }
}