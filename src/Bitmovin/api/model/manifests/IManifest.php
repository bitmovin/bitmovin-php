<?php

namespace Bitmovin\api\model\manifests;

use Bitmovin\api\model\Transferable;

interface IManifest extends Transferable
{
    public function getName();

    public function setName($name);

    public function getOutputs();

    public function setOutputs($outputs);
}