<?php

namespace mozartk\processCheck\Test;

use mozartk\processCheck\ProcessCheck;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class ProcessCheckTest extends TestCase
{
    public function testSetConfigPath()
    {
        $this->expectException('mozartk\processCheck\Exception\LoadConfigException');

        $testPath = "/config/file/not/exists";
        $process = new ProcessCheck();
        $process->setConfigPath($testPath);
        $this->assertEquals($testPath, $process->getConfigPath());
    }
    /*
        public function testCantReadableConfig()
        {
            $this->expectException('mozartk\processCheck\Exception\LoadConfigException');

            $testPath = "tests/config.imp.json";
            $process = new ProcessCheck();
            $process->setConfigPath($testPath);
            $this->assertEquals($testPath, $process->getConfigPath());

        }
    */
    public function testResultClassDoesNotExists()
    {
        $this->expectException('mozartk\processCheck\Exception\NotExistsParserResultException');

        $testPath = "tests/config.wrongclass.json";
        $process = new ProcessCheck();
        $process->setConfigPath($testPath);
        $process->run();
    }

    public function testResultJson()
    {
        $testPath = "tests/config.json";
        $process = new ProcessCheck();
        $process->setConfigPath($testPath);
        $result = $process->run();

        json_decode($result);
        $this->assertEquals(json_last_error(), JSON_ERROR_NONE);
    }

    public function testResultYaml()
    {
        $testPath = "tests/config.yaml.json";
        $process = new ProcessCheck();
        $process->setConfigPath($testPath);
        $result = $process->run();

        $res_arr = Yaml::parse($result);

        $this->assertTrue(is_array($res_arr));
    }

    public function testResultIni()
    {
        $testPath = "tests/config.ini.json";
        $process = new ProcessCheck();
        $process->setConfigPath($testPath);
        $result = $process->run();

        $this->assertTrue(is_array(parse_ini_string($result)));
    }

    public function testResultClassNotExists()
    {
        $this->expectException("mozartk\processCheck\Exception\NotExistsParserResultException");

        $testPath = "tests/config.wrongclass.json";
        $process = new ProcessCheck();
        $process->setConfigPath($testPath);
        $result = $process->run();
    }
}
