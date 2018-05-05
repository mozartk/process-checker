<?php

namespace mozartk\processCheck\Lib;
use Noodlehaus\Config as NConfig;

class Config extends NConfig implements \ArrayAccess
{
    public function __construct($path)
    {
        parent::__construct($path);
    }
}