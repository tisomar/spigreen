<?php
if (!isset($_class)) {
    trigger_error('você deve definir a classe $_class');
}

require_once QCOMMERCE_DIR . '/admin/includes/config.inc.php';
require_once QCOMMERCE_DIR . '/admin/includes/security.inc.php';

$_classPeer = $_class::PEER;
$_classQuery = $_class . 'Query';

$root = $_classQuery::create()->findRoot();
if (is_null($root)) {
    $root = new $_class();
    $root->setNome('Módulos');
    $root->makeRoot(); // make this node the root of the tree
    $root->save();
}

if ($request->query->has('id')) {
    $object = $_classPeer::retrieveByPK($request->query->get('id'));
} else {
    $object = new $_class();
}

if (!$object) {
     redirect_404admin();
}

$erros = array();

if ($request->getMethod() == 'POST') {
    $parent = $_classPeer::retrieveByPK($request->request->get('PARENT_ID'));
    $data = trata_post_array($request->request->get('data'));
    $object->setByArray($data);

    if ($object->isNew()) {
        $object->insertAsLastChildOf($parent);
    } else {
        if (!$parent->equals($object->getParent())) {
            $object->moveToLastChildOf($parent);
        }
    }

    if ($object->myValidate($erros) && !$erros) {
        $object->save();
        $parent->sortChildrens();

        $session->getFlashBag()->add('success', 'Registro armazenado com sucesso!');
        redirect_listagem('index.php');
    }

    if (count($erros)) {
        foreach ($erros as $erro) {
            $session->getFlashBag()->add('error', $erro);
        }
    }
}
