<?php

namespace mozartk\processCheck\Lib;

use mozartk\processCheck\Exception\LoadConfigException;
use Noodlehaus\Config as NConfig;

abstract class ConfigBasic
{
    const BASIC_CONFIGPATH = "./config.json";

    private $configPath = "";

    private $processList = array();
    private $outputMode = "";

    private $loaded = false;
    private $nconfig;

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

        $this->readConfig();
    }

    /**
     * Get informations from config arrays.
     *
     * @param  $configContents.
     */
    private function parsingConfig($configContents)
    {
        $this->setOutputMode($configContents['outputMode']);
        $this->makeProcessList($configContents['processList']);
    }

    public function setOutputMode($outputMode = "")
    {
        $this->outputMode = ucfirst($outputMode);
    }

    public function getOutputMode()
    {
        return $this->outputMode;
    }

    public function makeProcessList($processArray)
    {
        $this->processList = array();
        foreach($processArray as $key=>$val) {
            $this->processList[] = $val;
        }
    }

    public function getProcessList()
    {
        return $this->processList;
    }

    /**
     * Load Config file for processCheck
     */
    private function loadConfig()
    {
        $result = new NConfig($this->configPath);
        return $result;
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
}
