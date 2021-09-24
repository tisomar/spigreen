<?php


$_class = ParametroPeer::OM_CLASS;

if ($request->query->has('id')) {
    $object = ParametroPeer::retrieveByPK($request->query->get('id'));
    $pageTitle = $object->getNomeAmigavel();
} else {
    $pageTitle = "Parâmetros";
}

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
