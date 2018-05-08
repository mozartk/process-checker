<?php

namespace mozartk\processChecker;

use \Craftpip\ProcessHandler\ProcessHandler;
use mozartk\processChecker\Exception\NotExistsParserResultException;
use mozartk\processChecker\Process\JsonResult;
use mozartk\processChecker\Process\YamlResult;
use mozartk\processChecker\Process\IniResult;
use mozartk\processChecker\Exception\ProcessException;
use mozartk\processChecker\Lib\Config;

class ProcessChecker
{
    /**
     *
     */
    const RESULT_PARSER_NAMESPACE = "\\mozartk\\processChecker\\Process\\";

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
