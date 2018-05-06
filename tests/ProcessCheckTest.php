<?php

namespace mozartk\processCheck\Test;
use mozartk\processCheck\ProcessCheck;
use PHPUnit\Framework\TestCase;

class ProcessCheckTest extends TestCase
{

    public function testSetConfigPath()
    {
        $testPath = "/config/file/not/exists";
        $process = new ProcessCheck();
        $process->setConfigPath($testPath);
        $this->assertEquals($testPath, $process->getConfigPath());
    }

    public function testSetConfigPathSpace()
    {
        $process = new ProcessCheck();

        $process->setConfigPath(" ");
        $this->assertEquals(ProcessCheck::BASIC_CONFIGPATH, $process->getConfigPath());
    }

    public function testSetConfigPathExists()
    {
        $process = new ProcessCheck();

        $process->setConfigPath("./config.json");
        $this->assertEquals(ProcessCheck::BASIC_CONFIGPATH, $process->getConfigPath());
    }

    public function testRunNotExistsConfig()
    {
        $this->expectException('mozartk\processCheck\Exception\LoadConfigException');

        $process = new ProcessCheck();
        $process->setConfigPath("/mozartk/notExistsFiles");
        $process->run();
    }

    public function testCheckProcessValidJson()
    {
        $process = new ProcessCheck();
        $process->setConfigPath("./tests/config.json");
        $result = $process->run();

        $jsonResult = json_decode($result);
        $this->assertEquals(json_last_error(),JSON_ERROR_NONE);
    }
}
