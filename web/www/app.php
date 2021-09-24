<?php

/**
 * Helper debugger to log.
 *
 * @param $data
 */
function debug_to_log($data)
{
    $output = $data;

    if (is_array($output)) :
        $output = implode(',', $output);
    endif;

    file_put_contents(__DIR__ . '/log_' . date("j.n.Y") . '.log', $output, FILE_APPEND);
}

/**
 * Send debug code to the Javascript console
 *
 * @param $data
 */
function debug_to_console($data)
{
    if (is_array($data) || is_object($data)) :
        echo("<script>console.log('PHP: " . json_encode($data) . "');</script>");
    else :
        echo("<script>console.log('PHP: " . $data . "');</script>");
    endif;
}

// Configurações padrões de localidade
setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");

// // Fuso horário de Cuiabá, sendo -4hrs de deslocamento, GMT -4, SEM HORÁRIO DE VERÃO
// Fuso horário de Brasília, sendo -3hrs de deslocamento, GMT -3, SEM HORÁRIO DE VERÃO
date_default_timezone_set(timezone_name_from_abbr('', -3 * 3600, 1));

// Localização do arquivo de autoload do composer
$loader = require_once __DIR__ . '/../quality/vendor/autoload.php';


/**
 * USE MONOLOG FROM NOW ON FOR EXCEPTION HANDLING!
 */
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('debug-channel');
$logger->pushHandler(new StreamHandler('debug_app.log', Logger::DEBUG));

// Inclusão do arquivo de bootstrap
require_once __DIR__ . '/../quality/app/bootstrap.php';
