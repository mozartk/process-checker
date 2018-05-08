<?php

namespace mozartk\processChecker\Test;

use mozartk\processChecker\ProcessChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class ProcessCheckerTest extends TestCase
{
    /**
     * @expectedException \mozartk\processChecker\Exception\LoadConfigException
     */
    public function testSetConfigPath()
    {
        $testPath = "/config/file/not/exists";
        $process = new ProcessChecker();
        $process->setConfigPath($testPath);
        $this->assertEquals($testPath, $process->getConfigPath());
    }
    /*
        public function testCantReadableConfig()
        {
            $this->expectException('mozartk\processChecker\Exception\LoadConfigException');

            $testPath = "tests/config.imp.json";
            $process = new ProcessChecker();
            $process->setConfigPath($testPath);
            $this->assertEquals($testPath, $process->getConfigPath());

        }
    */

    public function testResultJson()
    {
        $testPath = "tests/config.json";
        $process = new ProcessChecker();
        $process->setConfigPath($testPath);
        $result = $process->run();

        json_decode($result);
        $this->assertEquals(json_last_error(), JSON_ERROR_NONE);
    }

    public function testResultYaml()
    {
        $testPath = "tests/config.yaml.json";
        $process = new ProcessChecker();
        $process->setConfigPath($testPath);
        $result = $process->run();

        $res_arr = Yaml::parse($result);

        $this->assertTrue(is_array($res_arr));
    }

    public function testResultIni()
    {
        $testPath = "tests/config.ini.json";
        $process = new ProcessChecker();
        $process->setConfigPath($testPath);
        $result = $process->run();

        $this->assertTrue(is_array(parse_ini_string($result)));
    }

    /**
     * @expectedException \mozartk\processChecker\Exception\NotExistsParserResultException
     */
    public function testResultClassNotExists()
    {
        $testPath = "tests/config.wrongclass.json";
        $process = new ProcessChecker();
        $process->setConfigPath($testPath);
        $result = $process->run();
    }
}
