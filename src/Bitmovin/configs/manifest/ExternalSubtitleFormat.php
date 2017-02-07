<?php

namespace Bitmovin\configs\manifest;


class ExternalSubtitleFormat
{
    /**
     * @var string
     */
    var $lang;

    /**
     * @var string
     */
    var $uri = null;

    /**
     * @var string[]
     */
    var $subtitleUrls = array();
}