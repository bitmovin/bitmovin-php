<?php

namespace Bitmovin\api\model\transfers;

use Bitmovin\api\model\encodings\Encoding;
use JMS\Serializer\Annotation as JMS;

class TransferEncoding extends AbstractTransfer
{
    /**
     * @JMS\Type("string")
     * @var  string UUID
     */
    private $encodingId;

    /**
     * TransferEncoding constructor.
     *
     * @param Encoding $encoding
     */
    public function __construct(Encoding $encoding)
    {
        $this->encodingId = $encoding->getId();
    }

    /**
     * @return string
     */
    public function getEncodingId()
    {
        return $this->encodingId;
    }

    /**
     * @param string $encodingId
     */
    public function setEncodingId($encodingId)
    {
        $this->encodingId = $encodingId;
    }
}