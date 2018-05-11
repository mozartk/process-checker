<?php

namespace mozartk\ProcessChecker\Results;


abstract class AbstractResult implements ResultInterface
{
    protected $parseData = array();
    public function parse($processName, $data = null)
    {
        $jsonData = array();
        $this->parseData[$processName] = array();
        foreach ($data as $process) {
            $jsonData['name'] = $process->getName();
            $jsonData['name_w'] = $process->getWindowTitle();
            $jsonData['cputime'] = $process->getCpuTime();
            $jsonData['pid'] = (int)$process->getPid();
            $jsonData['running'] = (boolean) $process->isRunning();

            $this->parseData[$processName][] = $jsonData;
        }
    }

    public function clear()
    {
        $this->parseData = array();
    }
}