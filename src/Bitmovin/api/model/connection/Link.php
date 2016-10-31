<?php

namespace Bitmovin\api\model\connection;

class Link
{
    /** @var  string URL */
    private $href;

    /** @var  string */
    private $title;

    /**
     * Link constructor.
     *
     * @param string $href
     */
    public function __construct($href)
    {
        $this->href = $href;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * @param string $href
     *
     * @return Link
     */
    public function setHref($href)
    {
        $this->href = $href;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Link
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
}