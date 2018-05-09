<?php

namespace mozartk\ProcessChecker\Process;

class ArrayResult extends AbstractResult implements ResultInterface
{
    public function get()
    {
        return $this->parseData;
    }
}
