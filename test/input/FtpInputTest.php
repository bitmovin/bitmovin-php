<?php

namespace Bitmovin\test\input;

use Bitmovin\api\model\inputs\InputConverterFactory;
use Bitmovin\input\FtpInput;
use Bitmovin\test\AbstractBitmovinApiTest;

class FtpInputTest extends AbstractBitmovinApiTest
{

    public function testFtpInput()
    {
        $input = new FtpInput('ftp://www.test.com/');
        $convert = InputConverterFactory::createFromFtpInput($input);
        $this->assertInstanceOf(\Bitmovin\api\model\inputs\FtpInput::class, $convert);
        $this->assertEquals('www.test.com', $convert->getHost());
        $this->assertEquals('', $convert->getUsername());
        $this->assertEquals('', $convert->getPassword());
        $this->assertEquals(false, $convert->isPassive());

        $input = new FtpInput('ftp://www.test.com:23/');
        $convert = InputConverterFactory::createFromFtpInput($input);
        $this->assertInstanceOf(\Bitmovin\api\model\inputs\FtpInput::class, $convert);
        $this->assertEquals('www.test.com', $convert->getHost());
        $this->assertEquals('', $convert->getUsername());
        $this->assertEquals('', $convert->getPassword());
        $this->assertEquals(23, $convert->getPort());

        $input = new FtpInput('ftp://www.test.com:23/');
        $input->passive = true;
        $convert = InputConverterFactory::createFromFtpInput($input);
        $this->assertInstanceOf(\Bitmovin\api\model\inputs\FtpInput::class, $convert);
        $this->assertEquals('www.test.com', $convert->getHost());
        $this->assertEquals('', $convert->getUsername());
        $this->assertEquals('', $convert->getPassword());
        $this->assertEquals(23, $convert->getPort());
        $this->assertEquals(true, $convert->isPassive());

        $input = new FtpInput('ftp://www.test.com/path');
        $convert = InputConverterFactory::createFromFtpInput($input);
        $this->assertInstanceOf(\Bitmovin\api\model\inputs\FtpInput::class, $convert);
        $this->assertEquals('www.test.com', $convert->getHost());
        $this->assertEquals('', $convert->getUsername());
        $this->assertEquals('', $convert->getPassword());

        $input = new FtpInput('ftp://www.test.com/path?query=1&query2=2');
        $convert = InputConverterFactory::createFromFtpInput($input);
        $this->assertInstanceOf(\Bitmovin\api\model\inputs\FtpInput::class, $convert);
        $this->assertEquals('www.test.com', $convert->getHost());
        $this->assertEquals('', $convert->getUsername());
        $this->assertEquals('', $convert->getPassword());

        $input = new FtpInput('ftp://username:password@www.test.com/path?query=1&query2=2');
        $convert = InputConverterFactory::createFromFtpInput($input);
        $this->assertInstanceOf(\Bitmovin\api\model\inputs\FtpInput::class, $convert);
        $this->assertEquals('www.test.com', $convert->getHost());
        $this->assertEquals('username', $convert->getUsername());
        $this->assertEquals('password', $convert->getPassword());
    }

}