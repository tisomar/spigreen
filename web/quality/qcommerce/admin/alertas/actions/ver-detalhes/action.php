<?php

if (!isset($_class)) {
    trigger_error('você deve definir a classe $_class');
}

$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());

$idAlerta = $container->getRequest()->get('id');

if (!isset($idAlerta) && !is_null($idAlerta) && !empty($idAlerta)) {
    trigger_error('ID não informado');
}

$object = DocumentoAlertaPeer::retrieveByPK($idAlerta);

if (!$object instanceof DocumentoAlerta) {
    trigger_error('ID não encontrado na base de dados.');
}
