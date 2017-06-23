<?php

namespace Bitmovin\api\resource\encodings\streams\muxings;

use Bitmovin\api\model\encodings\Encoding;
use Bitmovin\api\model\encodings\muxing\ProgressiveTSMuxing;
// use Bitmovin\api\resource\encodings\streams\muxings\drm\TsDrmContainer;

class ProgressiveTsMuxingResource extends MuxingResource
{

    /** @var Encoding */
    private $encoding;

    /**
     * ProgressiveTsMuxingResource constructor.
     *
     * @param Encoding $encoding
     * @param string   $baseUri
     * @param string   $modelClassName
     * @param string   $apiKey
     */
    public function __construct(Encoding $encoding, $baseUri, $modelClassName, $apiKey)
    {
        parent::__construct($baseUri, $modelClassName, $apiKey);
        $this->encoding = $encoding;
    }

    /**
     * @param ProgressiveTSMuxing $tsMuxing
     *
     * @return ProgressiveTSMuxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(ProgressiveTSMuxing $tsMuxing)
    {
        return parent::createMuxing($tsMuxing);
    }

    /**
     * @param ProgressiveTSMuxing $tsMuxing
     *
     * @return ProgressiveTSMuxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(ProgressiveTSMuxing $tsMuxing)
    {
        return parent::deleteMuxing($tsMuxing);
    }

    /**
     * @param ProgressiveTSMuxing $tsMuxing
     *
     * @return ProgressiveTSMuxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(ProgressiveTSMuxing $tsMuxing)
    {
        return parent::getMuxing($tsMuxing);
    }

    /**
     * @param integer $offset
     * @param integer $limit
     *
     * @return ProgressiveTSMuxing[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $tsMuxingId
     *
     * @return ProgressiveTSMuxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($tsMuxingId)
    {
        return parent::getMuxingById($tsMuxingId);
    }

    /**
     * @param $tsMuxingId
     *
     * @return ProgressiveTSMuxing
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($tsMuxingId)
    {
        return parent::deleteMuxingById($tsMuxingId);
    }
}
