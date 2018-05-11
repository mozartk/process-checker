<?php

namespace mozartk\ProcessChecker\Results;

class ArrayResult extends AbstractResult implements ResultInterface
{
    public function get()
    {
        return $this->parseData;
    }
}
