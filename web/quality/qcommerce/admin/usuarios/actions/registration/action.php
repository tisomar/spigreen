<?php

if (!isset($_class)) {
    trigger_error('você deve definir a classe $_class');
}

require_once QCOMMERCE_DIR . '/admin/includes/config.inc.php';
require_once QCOMMERCE_DIR . '/admin/includes/security.inc.php';

$_classPeer = $_class::PEER;
$_classQuery = $_class . 'Query';

/* @var $object Usuario */
if ($request->query->has('id')) {
    $object = $_classPeer::retrieveByPK($request->query->get('id'));
} else {
    $object = new $_class();
}

if (!$object) {
    redirect_404admin();
}

if ($object->isMaster() && !UsuarioPeer::getUsuarioLogado()->isMaster()) {
    redirect_404admin();
}

$erros = array();

if ($request->getMethod() == 'POST') {
    $data = $container->getRequest()->request->get('data');
    $object->setByArray($data);

    if ($object->myValidate($erros) && !$erros) {
        $object->save();

        $object->getPermissaoGrupoUsuarios()->delete();
        $object->initPermissaoGrupoUsuarios();

        if (isset($data['GRUPO_ID']) && count($data['GRUPO_ID'])) {
            foreach ($data['GRUPO_ID'] as $grupo_id) {
                $oPermissaoGrupoModulo = new PermissaoGrupoUsuario();
                $oPermissaoGrupoModulo->setGrupoId($grupo_id);
                $oPermissaoGrupoModulo->setUsuarioId($object->getId());
                $oPermissaoGrupoModulo->save();
            }
        }

        $session->getFlashBag()->add('success', 'Registro armazenado com sucesso!');
        redirect_listagem('index.php');
    }

    if (count($erros)) {
        foreach ($erros as $erro) {
            $session->getFlashBag()->add('error', $erro);
        }
    }
}

$idPermissaoModuloGrupoSelected = $object->getPermissaoGrupoUsuarios(PermissaoGrupoUsuarioQuery::create()->select(array('GrupoId')))->toArray();
