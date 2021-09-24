<?php

$rootDir = QCOMMERCE_DIR;

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', $request->getBaseUrl());
}

// Caminho da pasta pública
if (!defined('BASE_PATH')) {
    define('BASE_PATH', $request->getBasePath());
}

if (!defined('BASE_URL')) {
    define('BASE_URL', $request->getSchemeAndHttpHost() . BASE_PATH);
}

// XML com as dimensões das imagens utilizadas no site
if (!defined('IMAGE_CONFIG_FILE')) {
    define('IMAGE_CONFIG_FILE', $_SERVER['DOCUMENT_ROOT'] . BASE_PATH . '/resize/image-config.xml');
}

// Tamanho máximo de um breadcrumb (depois disso será cortado)
if (!defined('BREADCRUMB_LIMITE_CARACTERES')) {
    define('BREADCRUMB_LIMITE_CARACTERES', 120);
}

// Diretório de spool e-mails
if (!defined('SPOOL_DIR')) {
    define('SPOOL_DIR', __DIR__ . '/../swift/emails');
}

/**
 *
 * DEFINIÇÕES DE DIRETÓRIOS
 *
 */



if (!defined('CMS')) {
    define('CMS', 'admin');
}

// Expressões regulares
if (!defined('REG_DATE')) {
    define('REG_DATE', '(0[1-9]|[1,2][0-9]|3[0,1])/(0[1-9]|1[0-2])/(19[0-9][0-9]|2[0-9][0-9][0-9])');
}

if (!defined('REG_PASSWORD')) {
    define('REG_PASSWORD', '^.{6,}$');
}

if (!defined('REG_TEL')) {
    define('REG_TEL', '\([0-9]{2}\)[\s][0-9]{4,5}-[0-9]{4}');
}

if (!defined('REG_CEP')) {
    define('REG_CEP', '\d{5}-\d{3}');
}
