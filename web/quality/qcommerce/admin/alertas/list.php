<?php

$pageTitle = 'Alertas e Mensagens';
$_class = DocumentoAlertaPeer::OM_CLASS;
$_classQuery = 'DocumentoAlertaQuery';
$_classPeer = 'DocumentoAlertaPeer';

$preQuery   = $_classQuery::create()->orderByDataEnvio(Criteria::DESC);
$rowsPerPage = 50;

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
