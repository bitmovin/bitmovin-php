<?php

namespace Bitmovin\api\model\connection;

class ListResult implements IResult
{
    /** @var array */
    private $objectArray;

    public function __construct(array $objectArray)
    {
        $this->objectArray = $objectArray;
    }

    /**
     * @return array
     */
    public function getContent()
    {
        return $this->objectArray;
    }
}