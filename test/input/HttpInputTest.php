<?php


namespace input;


use Bitmovin\api\model\inputs\HttpsInput;
use Bitmovin\api\model\inputs\InputConverterFactory;
use Bitmovin\input\HttpInput;
use Bitmovin\test\AbstractBitmovinApiTest;

class HttpInputTest extends AbstractBitmovinApiTest
{

    public function testHttpInput()
    {
        $input = new HttpInput('http://www.test.com/');
        $convert = InputConverterFactory::createFromHttpInput($input);
        $this->assertInstanceOf(\Bitmovin\api\model\inputs\HttpInput::class, $convert);
        $this->assertEquals('www.test.com', $convert->getHost());
        $this->assertEquals('', $convert->getUsername());
        $this->assertEquals('', $convert->getPassword());

        $input = new HttpInput('http://www.test.com/path');
        $convert = InputConverterFactory::createFromHttpInput($input);
        $this->assertInstanceOf(\Bitmovin\api\model\inputs\HttpInput::class, $convert);
        $this->assertEquals('www.test.com', $convert->getHost());
        $this->assertEquals('', $convert->getUsername());
        $this->assertEquals('', $convert->getPassword());

        $input = new HttpInput('http://www.test.com/path?query=1&query2=2');
        $convert = InputConverterFactory::createFromHttpInput($input);
        $this->assertInstanceOf(\Bitmovin\api\model\inputs\HttpInput::class, $convert);
        $this->assertEquals('www.test.com', $convert->getHost());
        $this->assertEquals('', $convert->getUsername());
        $this->assertEquals('', $convert->getPassword());

        $input = new HttpInput('http://username:password@www.test.com/path?query=1&query2=2');
        $convert = InputConverterFactory::createFromHttpInput($input);
        $this->assertInstanceOf(\Bitmovin\api\model\inputs\HttpInput::class, $convert);
        $this->assertEquals('www.test.com', $convert->getHost());
        $this->assertEquals('username', $convert->getUsername());
        $this->assertEquals('password', $convert->getPassword());
    }

    public function testHttpsInput()
    {
        $input = new HttpInput('https://www.test.com/');
        $convert = InputConverterFactory::createFromHttpInput($input);
        $this->assertInstanceOf(HttpsInput::class, $convert);
        $this->assertEquals('www.test.com', $convert->getHost());
        $this->assertEquals('', $convert->getUsername());
        $this->assertEquals('', $convert->getPassword());

        $input = new HttpInput('https://www.test.com/path');
        $convert = InputConverterFactory::createFromHttpInput($input);
        $this->assertInstanceOf(HttpsInput::class, $convert);
        $this->assertEquals('www.test.com', $convert->getHost());
        $this->assertEquals('', $convert->getUsername());
        $this->assertEquals('', $convert->getPassword());

        $input = new HttpInput('https://www.test.com/path?query=1&query2=2');
        $convert = InputConverterFactory::createFromHttpInput($input);
        $this->assertInstanceOf(HttpsInput::class, $convert);
        $this->assertEquals('www.test.com', $convert->getHost());
        $this->assertEquals('', $convert->getUsername());
        $this->assertEquals('', $convert->getPassword());

        $input = new HttpInput('https://username:password@www.test.com/path?query=1&query2=2');
        $convert = InputConverterFactory::createFromHttpInput($input);
        $this->assertInstanceOf(HttpsInput::class, $convert);
        $this->assertEquals('www.test.com', $convert->getHost());
        $this->assertEquals('username', $convert->getUsername());
        $this->assertEquals('password', $convert->getPassword());
    }


}