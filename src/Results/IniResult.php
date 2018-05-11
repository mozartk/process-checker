<?php

namespace mozartk\ProcessChecker\Results;

use Matomo\Ini\IniWriter;

class IniResult extends AbstractResult implements ResultInterface
{
    public function parse($processName, $data = null)
    {
        foreach ($data as $idx=>$process) {
            $jsonData = array();
            $jsonData['name'] = $process->getName();
            $jsonData['name_w'] = $process->getWindowTitle();
            $jsonData['cputime'] = $process->getCpuTime();
            $jsonData['pid'] = (int)$process->getPid();
            $jsonData['running'] = (boolean) $process->isRunning();

            $withPidName = $processName.' ('.(int)$process->getPid().')';
            $this->parseData[$withPidName] = $jsonData;
        }
    }

    public function get()
    {
        $writer = new IniWriter();
        return $writer->writeToString($this->parseData);
    }
}
