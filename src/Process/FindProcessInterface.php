<?php
/**
 * FindProcessInterface.php
 * User: mozartk-mac
 * Date: 2018. 5. 11.
 * Time: PM 11:03
 */

namespace mozartk\ProcessChecker\Process;


interface FindProcessInterface
{
    public function findProcessByPID($pid);
    public function findProcessByName($processName);
}