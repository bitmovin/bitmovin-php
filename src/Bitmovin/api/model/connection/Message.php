<?php

namespace Bitmovin\api\model\connection;

class Message
{
    /** @var  string(enum MessageType) */
    private $type;

    /** @var  string */
    private $text;

    /** @var  string */
    private $field;

    /** @var Link[] */
    private $links = array();

    /** @var \stdClass */
    private $more;

    /**
     * Message constructor.
     *
     * @param string    $type
     * @param string    $text
     * @param string    $field
     * @param array     $links
     * @param \stdClass $more
     */
    public function __construct($type, $text, $field = NULL, array $links = array(), $more = NULL)
    {
        $this->type = $type;
        $this->text = $text;
        $this->field = $field;
        $this->links = $links;
        $this->more = $more;
    }

}