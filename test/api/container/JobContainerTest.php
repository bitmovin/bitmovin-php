<?php


namespace Bitmovin\test\api\container;


use Bitmovin\api\container\JobContainer;
use Bitmovin\configs\JobConfig;
use Bitmovin\output\GcsOutput;
use Bitmovin\test\AbstractBitmovinApiTest;

class JobContainerTest extends AbstractBitmovinApiTest
{

    public function testGcsOutputPath()
    {
        $jobContainer = new JobContainer();
        $jobContainer->job = new JobConfig();

        $output = new GcsOutput("mykey", "mykey", "testbucket");
        $output->prefix = 'test_prefix';
        $jobContainer->job->output = $output;
        $this->assertEquals('test_prefix/', $jobContainer->getOutputPath());

        $output = new GcsOutput("mykey", "mykey", "testbucket");
        $output->prefix = 'test_prefix/';
        $jobContainer->job->output = $output;
        $this->assertEquals('test_prefix/', $jobContainer->getOutputPath());

        $output = new GcsOutput("mykey", "mykey", "testbucket");
        $output->prefix = '/test_prefix/';
        $jobContainer->job->output = $output;
        $this->assertEquals('test_prefix/', $jobContainer->getOutputPath());

        $output = new GcsOutput("mykey", "mykey", "testbucket");
        $output->prefix = '/test_prefix/test';
        $jobContainer->job->output = $output;
        $this->assertEquals('test_prefix/test/', $jobContainer->getOutputPath());

        $output = new GcsOutput("mykey", "mykey", "testbucket");
        $output->prefix = 'test_prefix/test';
        $jobContainer->job->output = $output;
        $this->assertEquals('test_prefix/test/', $jobContainer->getOutputPath());

        $output = new GcsOutput("mykey", "mykey", "testbucket");
        $output->prefix = 'test_prefix/test/';
        $jobContainer->job->output = $output;
        $this->assertEquals('test_prefix/test/', $jobContainer->getOutputPath());
    }

}