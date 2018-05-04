<?php

namespace mozartk\processCheck;

use \Monolog\Logger as Logger;
use \Monolog\Handler\StreamHandler;
use \Craftpip\ProcessHandler\ProcessHandler;
use mozartk\processCheck\Process\JsonParsing;
use phpDocumentor\Reflection\Types\Integer;
use \Symfony\Component\Process\Process;
use mozartk\processCheck\Exception\LoadConfigException;

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
        $this->parser = new JsonParsing();
    }

    /**
     * @return mixed
     */
    public function getConfigPath()
    {
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

    /**
     * Load Config file for processCheck
     */
    private function getConfig()
    {
        $iniData = file_get_contents($this->configPath);
        $result = json_decode($iniData, true);

        try {
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new LoadConfigException("JSON_LOAD_ERROR : [".json_last_error().'] '.json_last_error_msg());
            }
        } catch(LoadConfigException $e) {
            return false;
        } catch(\Exception $e) {
            return false;
        }

        return $result;
    }

    /**
     * Get informations from config arrays.
     *
     * @param array $configContents Config arrays.
     */
    private function parsingConfig(array $configContents)
    {
        $this->processList = array();
        $parser = new JsonParsing();
        foreach($configContents['processList'] as $key=>$val) {
            $processList[] = $val;
        }

        $this->processList = $processList;
    }

    private function readConfig()
    {
        $data = $this->getConfig();
        $resultData = null;
        if($data !== false) {
            $resultData = $this->parsingConfig($data);
        } else {
            return false;
        }
    }

    private function findProcess($processName = "")
    {
        $processHandler = new ProcessHandler();
        $process = $processHandler->getAllProcesses();

        $pattern = '/\/'.$processName.'\s/';
        $pid = "";
        foreach($process as $key=>$val) {
            if(preg_match($pattern, $val->getName())){
                $pid = $val->getPid();
                break;
            }
        }

        return $pid;
    }

    private function getProcess($pid = -99)
    {
        $processHandler = new ProcessHandler();
        return $processHandler->getProcess($pid);
    }

    public function run()
    {
        $this->readConfig();
        foreach($this->processList as $key=>$val) {
            $pid  = $this->findProcess($val);
            $info = $this->getProcess($pid);
            $this->parser->parse($val, $info);
        }

        return $this->parser->get();
    }
}
