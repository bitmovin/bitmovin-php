<?php


namespace Bitmovin\configs\manifest;


use Bitmovin\configs\drm\CencDrm;

class DashOutputFormat extends AbstractOutputFormat
{

    /**
     * @var CencDrm
     */
    public $cenc = null;

}