<?php

namespace mozartk\processCheck;

use \Monolog\Logger as Logger;
use \Monolog\Handler\StreamHandler;
use \Craftpip\ProcessHandler\ProcessHandler;
use phpDocumentor\Reflection\Types\Integer;
use \Symfony\Component\Process\Process;
use mozartk\processCheck\Exception\LoadConfigException;

class ProcessCheck
{

    /**
     * ini path
     */
    protected $ini_path = "/Users/mozartk-mac/project/processCheck/src/config.json";

    private $processList = array();

    public function __construct()
    {
        $data = $this->getConfig();
        $resultData = $this->parsingConfig($data);
    }

    /**
     * Load Config file for processCheck
     */
    public function getConfig()
    {
        $iniData = file_get_contents($this->ini_path);
        $result = json_decode($iniData, true);

        try {
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new LoadConfigException("JSON_LOAD_ERROR : [".json_last_error().'] '.json_last_error_msg());
            }
        } catch(LoadConfigException $e) {

        } catch(\Exception $e) {

        }

        return $result;
    }


    /**
     * Get informations from config arrays.
     *
     * @param array $configContents Config arrays.
     */
    private function parsingConfig(array $configContents)
    {
        $this->processList = array();
        foreach($configContents['processList'] as $key=>$val) {
            $processList[] = $val;
        }

        $this->processList = $processList;
    }

    private function findProcess($processName = "")
    {
        $processHandler = new ProcessHandler();
        $process = $processHandler->getAllProcesses();

        $pattern = '/\/'.$processName.'\s/';
        $pid = "";
        foreach($process as $key=>$val) {
            if(preg_match($pattern, $val->getName())){
                $pid = $val->getPid();
                break;
            }
        }

        return $pid;
    }

    private function getProcess($pid = -99)
    {
        $processHandler = new ProcessHandler();
        return $processHandler->getProcess($pid);
    }

    public function run()
    {
        foreach($this->processList as $key=>$val) {
            $pid = $this->findProcess($val);
            $info = $this->getProcess($pid);

            print_r($info);
        }
    }

    /**
     * Friendly welcome
     *
     * @param string $phrase Phrase to return
     *
     * @return string Returns the phrase passed in
     */
    public function echoPhrase()
    {
        $processHandler = new ProcessHandler();
        $process = $processHandler->getAllProcesses();

        $log = new Logger('mozartk');
        $log->pushHandler(new StreamHandler('testlog.log', Logger::INFO));
        $result = array(
            "name" =>$process[0]->getName(),
            "pid" =>$process[0]->getPid(),
            "mem_used" =>$process[0]->getMemUsed()
        );
        $log->addInfo(json_encode($result));
    }
}
