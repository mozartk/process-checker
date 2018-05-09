<?php

namespace mozartk\ProcessChecker\Process;

class JsonResult extends AbstractResult implements ResultInterface
{
    public function get()
    {
        return json_encode($this->parseData);
    }
}
