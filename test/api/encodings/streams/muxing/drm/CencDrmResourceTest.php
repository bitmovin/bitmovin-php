<?php


namespace Bitmovin\test\api\encodings\streams\muxing\drm;


use Bitmovin\api\model\encodings\drms\AbstractDrm;
use Bitmovin\api\model\encodings\drms\CencDrm;
use Bitmovin\api\model\encodings\helper\EncodingOutput;

class CencDrmResourceTest extends AbstractDrmResourceTest
{

    /**
     * @return AbstractDrm
     */
    protected function create()
    {
        $output = new EncodingOutput($this->createOutput());
        $output->setOutputPath("");
        return new CencDrm("13", "234", [$output]);
    }

    /**
     * @return mixed
     */
    protected function getResource()
    {
        return $this->apiClient->encodings()
                               ->muxings($this->encoding)->fmp4Muxing()->drm($this->muxing)
                               ->cencDrm();
    }

    /**
     * @return string
     */
    protected function expectedClass()
    {
        return CencDrm::class;
    }
}