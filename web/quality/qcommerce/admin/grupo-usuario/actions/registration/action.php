<?php
if (!isset($_class)) {
    trigger_error('você deve definir a classe $_class');
}

require_once QCOMMERCE_DIR . '/admin/includes/config.inc.php';
require_once QCOMMERCE_DIR . '/admin/includes/security.inc.php';

$_classPeer = $_class::PEER;
$_classQuery = $_class . 'Query';

/* @var $object PermissaoGrupo */
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
    $data = $container->getRequest()->request->get('data');
    $object->setByArray($data);
    
    if ($object->myValidate($erros) && !$erros) {
        $object->save();
        
        $object->getPermissaoGrupoModulos()->delete();
        $object->initPermissaoGrupoModulos();
        
        foreach ($data['MODULO_ID'] as $modulo_id) {
            $oPermissaoGrupoModulo = new PermissaoGrupoModulo();
            $oPermissaoGrupoModulo->setModuloId($modulo_id);
            $oPermissaoGrupoModulo->setGrupoId($object->getId());
            $oPermissaoGrupoModulo->save();
        }

        $session->getFlashBag()->add('success', 'Registro armazenado com sucesso!');

        if ($request->request->get('redirectToOnSuccess') != '') {
            redirectTo($request->request->get('redirectToOnSuccess'));
        } else {
            redirectTo($config['routes']['list']);
        }
    }

    if (count($erros)) {
        foreach ($erros as $erro) {
            $session->getFlashBag()->add('error', $erro);
        }
    }
}

$idPermissaoModuloGrupoSelected = $object->getPermissaoGrupoModulos(PermissaoGrupoModuloQuery::create()->select(array('ModuloId')))->toArray();
