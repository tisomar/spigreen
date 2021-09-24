<?php 

// Habilitando erros
ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

// Verificar e prevenir acesso ao Debug nos controladores do Frontend, caso arquivo seja enviado acidentalmente.
/*if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !(in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1')) || php_sapi_name() === 'cli-server')
) {
    header('HTTP/1.0 403 Forbidden');
    exit('Você não tem permissão para acessar este arquivo.');
}*/

require_once 'app.php';