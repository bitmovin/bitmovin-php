<?php

namespace Bitmovin\api\model;


interface ITransfer
{
    public function getName();
    public function setName($name);
    public function getDescription();
    public function setDescription($description);
    public function getOutputs();
    public function setOutputs($outputs);
    public function getCloudRegion();
    public function setCloudRegion($cloudRegion);
}