<?php

namespace Bitmovin\api\model\manifests;


interface IManifest
{
    public function getName();
    public function setName($name);
    public function getOutputs();
    public function setOutputs($outputs);
}