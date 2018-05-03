<?php
/**
 * Created by IntelliJ IDEA.
 * User: mozartk-mac
 * Date: 2018. 5. 3.
 * Time: PM 10:26
 */

namespace mozartk\processCheck\Process;


interface ParserInterface
{
    public function parse($data);
    public function clear();
    public function get();
}