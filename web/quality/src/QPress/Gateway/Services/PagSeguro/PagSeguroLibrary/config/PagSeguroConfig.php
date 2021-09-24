<?php

/*
 ************************************************************************
 PagSeguro Config File
 ************************************************************************
 */

$PagSeguroConfig = array();

$PagSeguroConfig['environment'] = array();
$PagSeguroConfig['environment']['environment'] = "production";

$PagSeguroConfig['credentials'] = array();
$PagSeguroConfig['credentials']['email'] = Config::get('pagseguro_email');
$PagSeguroConfig['credentials']['token'] = Config::get('pagseguro_token');

$PagSeguroConfig['application'] = array();
$PagSeguroConfig['application']['charset'] = "UTF-8"; // UTF-8, ISO-8859-1

$PagSeguroConfig['log'] = array();
$PagSeguroConfig['log']['active'] = false;
$PagSeguroConfig['log']['fileLocation'] = "";
