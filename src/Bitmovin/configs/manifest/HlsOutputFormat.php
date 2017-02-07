<?php


namespace Bitmovin\configs\manifest;


class HlsOutputFormat extends AbstractHlsOutput
{
    /**
     * @var ExternalSubtitleFormat[]
     */
    public $vttSubtitles = array();
}