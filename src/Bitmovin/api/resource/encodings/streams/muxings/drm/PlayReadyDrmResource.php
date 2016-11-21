<?php

namespace Bitmovin\api\resource\encodings\streams\muxings\drm;

use Bitmovin\api\model\encodings\drms\CencDrm;
use Bitmovin\api\model\encodings\drms\PlayReadyDrm;

class PlayReadyDrmResource extends DrmResource
{

    /**
     * @param PlayReadyDrm $drm
     *
     * @return PlayReadyDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(PlayReadyDrm $drm)
    {
        return parent::createDrm($drm);
    }

    /**
     * @param PlayReadyDrm $drm
     *
     * @return PlayReadyDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(PlayReadyDrm $drm)
    {
        return parent::deleteDrm($drm);
    }

    /**
     * @param PlayReadyDrm $drm
     *
     * @return PlayReadyDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(PlayReadyDrm $drm)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::getDrmById($drm->getId());
    }

    /**
     * @param integer $offset
     * @param integer $limit
     * @return CencDrm[]
     */
    public function listPage($offset = 0, $limit = 25)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->listResourcePage($offset, $limit);
    }

    /**
     * @param $drmId
     *
     * @return PlayReadyDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($drmId)
    {
        return parent::getDrmById($drmId);
    }

    /**
     * @param $drmId
     *
     * @return PlayReadyDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($drmId)
    {
        return parent::deleteDrmById($drmId);
    }

}