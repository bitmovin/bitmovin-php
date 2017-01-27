<?php


namespace Bitmovin\configs\manifest;


class HlsOutputFormat extends AbstractOutputFormat
{
    /**
     * @var string
     */
    public $name = "stream.m3u8";

    /**
     * @var HlsConfigurationFileNaming[]
     */
    public $hlsConfigurationFileNaming = array();

}