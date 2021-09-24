<?php 

namespace QPress\ErrorHandler;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

use Monolog\Handler\RotatingFileHandler;

class ErrorHandler {

    private $types = array (
        E_ERROR              => 'Fatal Error',
        E_WARNING            => 'Warning',
        E_PARSE              => 'Parsing Error',
        E_NOTICE             => 'Notice',
        E_CORE_ERROR         => 'Core Error',
        E_CORE_WARNING       => 'Core Warning',
        E_COMPILE_ERROR      => 'Compile Error',
        E_COMPILE_WARNING    => 'Compile Warning',
        E_USER_ERROR         => 'User Error',
        E_USER_WARNING       => 'User Warning',
        E_USER_NOTICE        => 'User Notice',
        E_STRICT             => 'Runtime Notice',
        E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
    );

    public function register($env) {

        if ($env == 'dev' || !error_reporting()) {
            return;
        }

        //error_reporting(E_ALL);
        ini_set('display_errors', 0);

        register_shutdown_function(array($this, 'shutdown'));

    }


    public function shutdown(){

        /*$error = error_get_last();

        if (!is_null($error)) {

            $error['typedescription'] = $this->types[$error['type']];

            $filename = sprintf('%s/phperror-%s.log', realpath(__DIR__ . DS . '..' . DS . '..' . DS . '..' . DS . 'var' . DS . 'logs'), date('Y-m-d'));

            $oRotatingFile = new RotatingFileHandler($filename, 0);
            $oRotatingFile->setFormatter(new LineFormatter("[%datetime%] %message% | %context%\n"));

            $logger = new Logger('ErrorHandler');
            $logger->pushHandler($oRotatingFile);

            $logger->addError($error['message'], $error);

            if ($error['type'] == E_USER_ERROR) {
                try {
                    \Qmail::enviaMensagem('rafael.cordeiro@qualitypress.com.br', '[LOG-SYSTEM]['.$_SERVER['SERVER_NAME'].'] ' . $error['typedescription'], $this->toTable($error));
                } catch (\Swift_SwiftException $e) {
                }
            }
        }*/

    }

    private function toTable($error) {

        $html = "<table style='border: 2px solid #bbbbbb' cellpadding='5' cellspacing='5'>";

        foreach ($error as $key => $error) {
            $html .= "<tr>";
            $html .=    "<td style='border: 1px solid #dddddd; font: normal 14px Calibri;'><b>" . $key . "</b></td>";
            $html .=    "<td style='border: 1px solid #dddddd; font: normal 14px Calibri;'>" . $error . "</td>";
            $html .= "</tr>";
        }

        $html .= "</table>";

        return $html;

    }
}
