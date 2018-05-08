<?php

namespace mozartk\processChecker\Process;

use Symfony\Component\Yaml\Yaml;

class YamlResult implements ResultInterface
{
    private $parseData = array();
    public function parse($processName, $data = null)
    {
        foreach($data as $idx=>$process) {
            $jsonData = array();
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

    public function get()
    {
        return Yaml::dump($this->parseData, 5);
    }
}