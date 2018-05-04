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
}
