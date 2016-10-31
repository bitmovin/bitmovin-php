<?php


namespace Bitmovin\test\api\enum;


use Bitmovin\api\enum\codecConfigurations\BAdapt;
use Bitmovin\test\AbstractBitmovinApiTest;

/**
 * Class EnumTest
 * @package enum
 * @test
 */
class EnumTest extends AbstractBitmovinApiTest
{

    public function testEnum()
    {
        $array = BAdapt::getAvailableValues();
        $this->assertContains('FAST', $array);
    }

}