<?php


namespace Bitmovin\configs\manifest;


class HlsFMP4OutputFormat extends AbstractOutputFormat
{

    /**
     * @var string
     */
    public $name = "streamFMP4.m3u8";

    /**
     * @var HlsConfigurationFileNaming[]
     */
    public $hlsConfigurationFileNaming = array();

}