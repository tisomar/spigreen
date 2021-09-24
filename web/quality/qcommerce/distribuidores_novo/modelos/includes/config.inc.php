<?php

$GLOBALS['txtTitle']['sms']    = 'SMS';
$GLOBALS['txtTitle']['email']  = 'E-mail';
$GLOBALS['txtTitle']['agendamento']  = 'Agendamento';
$GLOBALS['txtTitle']['perda']  = 'Perda';

if (isset($_GET['tpTemplate']) && $_GET['tpTemplate'] <> '') {
    $tipoTemplate = $_GET['tpTemplate'];
} else {
    $tipoTemplate = $args[0];
}

$txtTitle       = $txtTitle[$tipoTemplate];

$novoUrl            = $root_path . '/distribuidores_novo/modelos/cadastro/' . $tipoTemplate;
$template_root_path = '/distribuidores_novo/templates/' . $tipoTemplate;
$urlForm            = $novoUrl;

$limitCaracteres = '';
if (strtoupper($tipoTemplate) == 'SMS') {
    $limitCaracteres = $configSmsLimitCaracteres;
}
