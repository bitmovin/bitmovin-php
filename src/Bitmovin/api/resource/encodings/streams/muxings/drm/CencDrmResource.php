<?php

namespace Bitmovin\api\resource\encodings\streams\muxings\drm;

use Bitmovin\api\model\encodings\drms\CencDrm;

class CencDrmResource extends DrmResource
{

    /**
     * @param CencDrm $drm
     *
     * @return CencDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function create(CencDrm $drm)
    {
        return parent::createDrm($drm);
    }

    /**
     * @param CencDrm $drm
     *
     * @return CencDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function delete(CencDrm $drm)
    {
        return parent::deleteDrm($drm);
    }

    /**
     * @param CencDrm $drm
     *
     * @return CencDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function get(CencDrm $drm)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::getDrmById($drm->getId());
    }

    /**
     * @return CencDrm[]
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function listAll()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::listAllDrms();
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
     * @return CencDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function getById($drmId)
    {
        return parent::getDrmById($drmId);
    }

    /**
     * @param $drmId
     *
     * @return CencDrm
     * @throws \Bitmovin\api\exceptions\BitmovinException
     */
    public function deleteById($drmId)
    {
        return parent::deleteDrmById($drmId);
    }

}