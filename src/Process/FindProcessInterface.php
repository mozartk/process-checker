<?php

namespace mozartk\ProcessChecker\Process;


interface FindProcessInterface
{
    public function findProcessByPID($pid);
    public function findProcessByName($processName);
}