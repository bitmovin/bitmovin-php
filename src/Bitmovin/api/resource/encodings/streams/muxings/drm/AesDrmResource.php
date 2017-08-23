<?php

namespace Bitmovin\api\resource\encodings\streams\muxings\drm;

use Bitmovin\api\model\encodings\drms\AesDrm;

class AesDrmResource extends DrmResource
{
    /**
     * @param AesDrm $drm
     *
     * @return AesDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(AesDrm $drm)
    {
        return parent::createDrm($drm);
    }

    /**
     * @param AesDrm $drm
     *
     * @return AesDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(AesDrm $drm)
    {
        return parent::deleteDrm($drm);
    }

    /**
     * @param AesDrm $drm
     *
     * @return AesDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(AesDrm $drm)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::getDrmById($drm->getId());
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return AesDrm[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $drmId
     *
     * @return AesDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($drmId)
    {
        return parent::getDrmById($drmId);
    }

    /**
     * @param $drmId
     *
     * @return AesDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($drmId)
    {
        return parent::deleteDrmById($drmId);
    }
}