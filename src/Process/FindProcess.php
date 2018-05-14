<?php
/**
 * Process.php
 * User: mozartk-mac
 * Date: 2018. 5. 11.
 * Time: PM 10:50
 */

namespace mozartk\ProcessChecker\Process;

use mozartk\ProcessFinder\ProcessFinder as ProcessHandler;

class FindProcess implements FindProcessInterface
{
    public function findProcessByPID($pid = 0)
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

    public function findProcessByName($processName = "")
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
}