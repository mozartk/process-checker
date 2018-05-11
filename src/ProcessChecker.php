<?php

namespace mozartk\ProcessChecker;

use \Craftpip\ProcessHandler\ProcessHandler;
use mozartk\ProcessChecker\Exception\NotExistsParserResultException;
use mozartk\ProcessChecker\Process\JsonResult;
use mozartk\ProcessChecker\Process\YamlResult;
use mozartk\ProcessChecker\Process\IniResult;
use mozartk\ProcessChecker\Exception\ProcessException;
use mozartk\ProcessChecker\Lib\Config;

class ProcessChecker
{
    /**
     *
     */
    const RESULT_PARSER_NAMESPACE = "\\mozartk\\ProcessChecker\\Process\\";

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
     * @param string $processName
     * @return array
     */
    private function findProcess($processName = "")
    {
        $processHandler = new ProcessHandler();
        $process = $processHandler->getAllProcesses();

        $pattern = '/'.$processName.'/';
        $pid = array();

        foreach ($process as $key=>$val) {
            if (preg_match($pattern, $val->getName())) {
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
        if (is_numeric($pid)) {
            $pids[] = $pid;
        } elseif (is_array($pid)) {
            $pids = $pid;
        } else {
            throw new ProcessException("pid type is wrong.");
        }

        foreach ($pids as $p) {
            $processHandler->setPid($p);
            $result[] = $processHandler->getProcess();
        }

        return $result;
    }

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
