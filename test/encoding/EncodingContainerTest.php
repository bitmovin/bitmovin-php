<?php


namespace Bitmovin\test\encoding;


use Bitmovin\api\container\EncodingContainer;
use Bitmovin\input\FtpInput;
use Bitmovin\input\HttpInput;
use Bitmovin\test\AbstractBitmovinApiTest;

class EncodingContainerTest extends AbstractBitmovinApiTest
{

    public function testHttpInputEncoding()
    {
        $httpInput = new HttpInput("http://www.test.com/my_path");
        $encodingContainer = new EncodingContainer(new \Bitmovin\api\model\inputs\HttpInput("www.test.com"), $httpInput);
        self::assertEquals('/my_path', $encodingContainer->getInputPath());

        $httpInput = new HttpInput("http://www.test.com/my_path?123");
        $encodingContainer = new EncodingContainer(new \Bitmovin\api\model\inputs\HttpInput("www.test.com"), $httpInput);
        self::assertEquals('/my_path?123', $encodingContainer->getInputPath());

        $httpInput = new HttpInput("http://www.test.com/my_path/temp/?123&test=3");
        $encodingContainer = new EncodingContainer(new \Bitmovin\api\model\inputs\HttpInput("www.test.com"), $httpInput);
        self::assertEquals('/my_path/temp/?123&test=3', $encodingContainer->getInputPath());
    }

    public function testFtpInputEncoding()
    {
        $httpInput = new FtpInput("ftp://www.test.com/my_path");
        $encodingContainer = new EncodingContainer(new \Bitmovin\api\model\inputs\FtpInput("www.test.com", '', ''), $httpInput);
        self::assertEquals('/my_path', $encodingContainer->getInputPath());

        $httpInput = new FtpInput("ftp://www.test.com/my_path?123");
        $encodingContainer = new EncodingContainer(new \Bitmovin\api\model\inputs\FtpInput("www.test.com", '', ''), $httpInput);
        self::assertEquals('/my_path?123', $encodingContainer->getInputPath());

        $httpInput = new FtpInput("ftp://www.test.com/my_path/temp/?123&test=3");
        $encodingContainer = new EncodingContainer(new \Bitmovin\api\model\inputs\FtpInput("www.test.com", '', ''), $httpInput);
        self::assertEquals('/my_path/temp/?123&test=3', $encodingContainer->getInputPath());
    }

}