<?php

namespace mozartk\ProcessChecker;

use mozartk\ProcessChecker\Exception\NotExistsParserResultException;
use mozartk\ProcessChecker\Results\JsonResult;
use mozartk\ProcessChecker\Results\YamlResult;
use mozartk\ProcessChecker\Results\IniResult;
use mozartk\ProcessChecker\Process\FindProcess;
use mozartk\ProcessChecker\Lib\Config;

class ProcessChecker
{
    /**
     *
     */
    const RESULT_PARSER_NAMESPACE = "\\mozartk\\ProcessChecker\\Results\\";

    /**
     * Result Class
     *
     * @var
     */
    private $parser;

    private $config;

    public function __construct()
    {
        $this->config = new Config();
    }

    public function setConfigPath($path)
    {
        $this->config->setConfigPath($path);
    }

    public function getConfigPath()
    {
        return $this->config->getConfigPath();
    }

    /**
     * set process names list for search
     *
     * @param array $processArr
     */
    public function setProcessName(array $processArr)
    {
        foreach($processArr as $val) {
            $this->config->addProcessName($val);
        }
    }

    /**
     * set output type
     *
     * @param $outputType
     */
    public function setOutputMode($outputType)
    {
        $this->config->setOutputMode($outputType);
    }

    private function loadParser($mode)
    {
        $className = self::RESULT_PARSER_NAMESPACE.ucfirst($mode)."Result";
        $result = class_exists($className);
        if ($result === false) {
            throw new NotExistsParserResultException("Result ClassName does not exist.");
        }

        return new $className();
    }

    /**
     * Find Process by ProcessName
     *
     * @param string $processName
     * @return array
     */
    private function findProcess($processName = "")
    {
        $process = new FindProcess();
        $pids = $process->findProcessByName($processName);

        return $pids;
    }

    /**
     * Find Process by PID
     *
     * @param int $pid
     * @return array
     */
    private function getProcess($pid = -99)
    {
        $process = new FindProcess();
        $result = $process->findProcessByPID($pid);

        return $result;
    }

    /**
     * Get final results
     *
     * @return mixed
     * @throws NotExistsParserResultException
     */
    public function run()
    {
        $this->parser = $this->loadParser($this->config->getOutputMode());
        $this->parser->clear();
        foreach ($this->config->getProcessList() as $key=>$val) {
            $pid  = $this->findProcess($val);
            $info = $this->getProcess($pid);
            $this->parser->parse($val, $info);
        }

        return $this->parser->get();
    }
}
