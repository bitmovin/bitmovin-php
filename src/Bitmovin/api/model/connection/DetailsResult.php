<?php

namespace Bitmovin\api\model\connection;

class DetailsResult implements IResult
{
    private $objectDetails;

    public function __construct(\stdClass $objectDetails)
    {
        $this->objectDetails = $objectDetails;
    }

    /**
     * @return \stdClass
     */
    public function getContent()
    {
        return $this->objectDetails;
    }
}