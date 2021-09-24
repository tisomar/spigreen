<?php

if (!isset($_class)) :
    trigger_error('você deve definir a classe $_class');
endif;

require_once QCOMMERCE_DIR . '/admin/includes/config.inc.php';
require_once QCOMMERCE_DIR . '/admin/includes/security.inc.php';

$erros = array();

$_classPeer = $_class::PEER;

if ($request->query->has('id')) :
    $object = $_classPeer::retrieveByPK($request->query->get('id'));
else :
    $object = new $_class();
endif;

if ($request->getMethod() == 'POST') :
    $parent = $_classPeer::retrieveByPK($request->request->get('PARENT_ID'));

    $data = trata_post_array($request->request->get('data'));

    $object->setByArray($data);

    if ($object->myValidate($erros) && !$erros) :
        $object->save();

        $session->getFlashBag()->add('success', 'Registro armazenado com sucesso!');

        if ($request->request->get('redirectToOnSuccess') != '') :
            redirectTo($request->request->get('redirectToOnSuccess'));
        else :
            redirectTo($config['routes']['list']);
        endif;
        exit;
    endif;

    if (count($erros)) :
        foreach ($erros as $erro) :
            $session->getFlashBag()->add('error', $erro);
        endforeach;
    endif;
endif;
