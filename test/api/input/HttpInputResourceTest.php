<?php


namespace Bitmovin\test\api\input;


use Bitmovin\api\model\inputs\HttpInput;
use Bitmovin\api\model\inputs\Input;

class HttpInputResourceTest extends AbstractInputResourceTest
{

    /**
     * @return Input
     */
    protected function createInput()
    {
        $input = new HttpInput("test");
        return $input;
    }

    /**
     * @return mixed
     */
    protected function getResource()
    {
        return $this->apiClient->inputs()->http();
    }

    /**
     * @return string
     */
    protected function expectedClass()
    {
        return HttpInput::class;
    }
}