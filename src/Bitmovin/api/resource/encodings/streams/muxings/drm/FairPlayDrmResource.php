<?php

namespace Bitmovin\api\resource\encodings\streams\muxings\drm;

use Bitmovin\api\model\encodings\drms\FairPlayDrm;

class FairPlayDrmResource extends DrmResource
{

    /**
     * @param FairPlayDrm $drm
     *
     * @return FairPlayDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(FairPlayDrm $drm)
    {
        return parent::createDrm($drm);
    }

    /**
     * @param FairPlayDrm $drm
     *
     * @return FairPlayDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(FairPlayDrm $drm)
    {
        return parent::deleteDrm($drm);
    }

    /**
     * @param FairPlayDrm $drm
     *
     * @return FairPlayDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(FairPlayDrm $drm)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::getDrmById($drm->getId());
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return FairPlayDrm[]
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
     * @return FairPlayDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($drmId)
    {
        return parent::getDrmById($drmId);
    }

    /**
     * @param $drmId
     *
     * @return FairPlayDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($drmId)
    {
        return parent::deleteDrmById($drmId);
    }

}