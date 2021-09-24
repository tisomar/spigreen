<?php

// Configurações
set_time_limit(0);
include_once __DIR__ . '/../includes/include_config.inc.php';

// Envio de e-mails em spool
$spool          = new Swift_FileSpool(SPOOL_DIR);
$spoolTransport = Swift_SpoolTransport::newInstance($spool);

// Criação do transportador em tempo real de e-mails
$sender = Qmail::createMailingTransport();

$spool->setMessageLimit(25);
$spool->setRetryLimit(3);
$spool->setTimeLimit(180);
$spool->flushQueue($sender);
