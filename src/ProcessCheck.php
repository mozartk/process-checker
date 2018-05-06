<?php

namespace mozartk\processCheck;

use \Craftpip\ProcessHandler\ProcessHandler;
use mozartk\processCheck\Process\JsonResult;
use mozartk\processCheck\Process\YamlResult;
use mozartk\processCheck\Process\IniResult;
use mozartk\processCheck\Exception\LoadConfigException;
use mozartk\processCheck\Exception\ProcessException;
use mozartk\processCheck\Lib\Config;

class ProcessCheck
{

    /**
     * ini path
     */
    const BASIC_CONFIGPATH = "./config.json";
    protected $configPath = "";

    private $parser;

    private $processList = array();

    public function __construct()
    {
        $this->parser = new JsonResult();
    }

    /**
     * @return mixed
     */
    public function getConfigPath()
    {
        if(trim($this->configPath) === "") {
            $this->configPath = self::BASIC_CONFIGPATH;
        }

        return $this->configPath;
    }

    /**
     * Set path for load Config files.
     *
     * @param String $config_path
     */
    public function setConfigPath($config_path = "")
    {
        if(trim($config_path) === "") {
            $config_path = self::BASIC_CONFIGPATH;
        }

        $this->configPath = $config_path;
    }

    private function checkIniFiles($config_path)
    {
        $exists = file_exists($config_path);
        $readable = is_readable($config_path);

        if(!$exists) {
            throw new LoadConfigException("The configuration file does not exist.");
        }

        if(!$readable) {
            throw new LoadConfigException("Cannot read configuration file.");
        }

        return true;
    }

    /**
     * Load Config file for processCheck
     */
    private function loadConfig()
    {
        $result = new Config($this->configPath);
        return $result;
    }

    /**
     * Get informations from config arrays.
     *
     * @param  $configContents.
     */
    private function parsingConfig(Config $configContents)
    {
        $this->processList = array();
        foreach($configContents['processList'] as $key=>$val) {
            $this->processList[] = $val;
        }
    }

    private function readConfig()
    {
        $this->checkIniFiles($this->configPath);
        $data = $this->loadConfig();
        $this->parsingConfig($data);

        if(is_array($this->processList)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $processName
     * @return array
     */
    private function findProcess($processName = "")
    {
        $processHandler = new ProcessHandler();
        $process = $processHandler->getAllProcesses();

        $pattern = '/'.$processName.'/';
        $pid = array();

        foreach($process as $key=>$val) {
            if(preg_match($pattern, $val->getName())){
                $pid[] = $val->getPid();
            }
        }

        return $pid;
    }

    /**
     * @param mixed $pid
     * @return array
     * @throws ProcessException
     * @throws \Craftpip\ProcessHandler\Exception\ProcessHandlerException
     */

    private function getProcess($pid = -99)
    {
        $pids = array();
        $result = array();
        $processHandler = new ProcessHandler();
        if(is_numeric($pid)) {
            $pids[] = $pid;
        } else if(is_array($pid)){
            $pids = $pid;
        } else {
            throw new ProcessException("pid type is wrong.");
        }

        foreach($pids as $p) {
            $processHandler->setPid($p);
            $result[] = $processHandler->getProcess();
        }

        return $result;
    }

    public function run()
    {
        if($this->readConfig()){
            $this->parser->clear();
            foreach($this->processList as $key=>$val) {
                $pid  = $this->findProcess($val);
                $info = $this->getProcess($pid);
                $this->parser->parse($val, $info);
            }

            return $this->parser->get();
        } else {
            return false;
        }
    }
}
