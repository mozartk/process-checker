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
    protected $ini_path = "./config.json";
    private $parser;

    private $processList = array();

    public function __construct()
    {
        $this->parser = new JsonParsing();
    }

    /**
     * @return mixed
     */
    public function getIniPath()
    {
        return $this->ini_path;
    }

    /**
     * @param mixed $ini_path
     */
    public function setIniPath($ini_path)
    {
        $this->ini_path = $ini_path;
    }

    /**
     * Load Config file for processCheck
     */
    private function getConfig()
    {
        $iniData = file_get_contents($this->ini_path);
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
        $data = array();
        foreach($this->processList as $key=>$val) {
            $pid = $this->findProcess($val);
            $info = $this->getProcess($pid);
            $this->parser->parse($info);
        }

        return $this->parser->get();
    }
}
