<?php


namespace Bitmovin\test\input;


use Bitmovin\api\model\inputs\FtpInput;
use Bitmovin\api\model\inputs\HttpInput;
use Bitmovin\api\model\inputs\HttpsInput;
use Bitmovin\test\AbstractBitmovinApiTest;

class InputEqualTest extends AbstractBitmovinApiTest
{

    public function testEqualHttp()
    {
        $input1 = new HttpInput("www.test.com");
        $this->assertTrue($input1->equals($input1));
        $input2 = new HttpInput("www.test.com");
        $this->assertTrue($input1->equals($input2));
        $this->assertTrue($input1->equals($input1));
        $input2 = new HttpInput("www.test2.com");
        $this->assertFalse($input1->equals($input2));
        $this->assertFalse($input2->equals($input1));

        $input2 = new HttpsInput("www.test.com");
        $this->assertFalse($input1->equals($input2));
        $this->assertFalse($input2->equals($input1));
    }

    public function testEqualFtp()
    {
        $input1 = new FtpInput("www.test.com", '', '');
        $this->assertTrue($input1->equals($input1));
        $input2 = new FtpInput("www.test.com", '', '');
        $this->assertTrue($input1->equals($input2));
        $this->assertTrue($input1->equals($input1));
        $input2 = new FtpInput("www.test2.com", '', '');
        $this->assertFalse($input1->equals($input2));
        $this->assertFalse($input2->equals($input1));
    }

}