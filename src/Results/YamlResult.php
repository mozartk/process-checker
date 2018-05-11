<?php

namespace mozartk\ProcessChecker\Results;

use Symfony\Component\Yaml\Yaml;

class YamlResult extends AbstractResult implements ResultInterface
{
    public function get()
    {
        return Yaml::dump($this->parseData, 5);
    }
}