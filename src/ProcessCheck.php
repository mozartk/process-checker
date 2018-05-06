<?php

namespace mozartk\processCheck;

use \Craftpip\ProcessHandler\ProcessHandler;
use mozartk\processCheck\Exception\NotExistsParserResultException;
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

    /**
     *
     */
    const RESULT_PARSER_NAMESPACE = "\\mozartk\\processCheck\\Process\\";

    /**
     * Set Output mode
     *
     * @var String
     */
    private $outputMode = "Json";

    /**
     * Stores the configuration data
     *
     * @var array
     */
    private $processList = array();


    /**
     * Result Class
     *
     * @var
     */
    private $parser;


    public function __construct()
    {
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
        $this->setOutputMode($configContents['outputMode']);
        $this->makeProcessList($configContents['processList']);
    }

    private function setOutputMode($outputMode = "")
    {
        $this->outputMode = ucfirst($outputMode);
    }

    private function makeProcessList($processArray)
    {
        $this->processList = array();
        foreach($processArray as $key=>$val) {
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

    private function loadParser($mode)
    {
        $className = self::RESULT_PARSER_NAMESPACE.ucfirst($mode)."Result";
        $result = class_exists($className);
        if($result === false) {
            throw new NotExistsParserResultException("Result ClassName does not exist.");
        }

        return new $className();
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
            $this->parser = $this->loadParser($this->outputMode);
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
