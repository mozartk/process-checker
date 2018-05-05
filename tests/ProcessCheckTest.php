<?php

namespace mozartk\processCheck\Test;
use mozartk\processCheck\ProcessCheck;
use PHPUnit\Framework\TestCase;

class ProcessCheckTest extends TestCase
{

    public function testSetConfigPath()
    {
        $testPath = "/a/b/c/d";
        $process = new ProcessCheck();
        $process->setConfigPath($testPath);
        $this->assertEquals($testPath, $process->getConfigPath());

        $process->setConfigPath(" ");
        $this->assertEquals(ProcessCheck::BASIC_CONFIGPATH, $process->getConfigPath());
    }

    public function testRunNotExistsConfig()
    {
        $this->expectException('mozartk\processCheck\Exception\LoadConfigException');

        $process = new ProcessCheck();
        $process->setConfigPath("/mozartk/notExistsFiles");
        $process->run();

    }
}
