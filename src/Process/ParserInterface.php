<?php

namespace mozartk\processCheck\Process;


interface ParserInterface
{
    public function parse($processName, $data);
    public function clear();
    public function get();
}