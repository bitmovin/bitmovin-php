<?php

namespace Bitmovin\api\model\connection;

class DeleteResult implements IResult
{
    /** @var string */
    private $resourceId;

    public function __construct($resourceId)
    {
        $this->resourceId = $resourceId;
    }

    /**
     * @return \stdClass
     */
    public function getContent()
    {
        return $this->resourceId;
    }
}