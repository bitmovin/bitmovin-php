<?php


namespace Bitmovin\configs\manifest;


class HlsOutputFormat extends AbstractHlsOutput
{
    /**
     * @JSM\Type("array<Bitmovin\configs\manifest\ExternalSubtitleFormat>")
     * @var ExternalSubtitleFormat[]
     */
    var $vttSubtitles = array();
}