<?php

/* @var $object Rede */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

$form->addElement(new Element\Textbox("Nome:", "data[NOME]", array(
    "value" => $object->getNome(),
    "required" => true
)));

$collPermissaoModulo = PermissaoModuloPeer::getModulosContratados(1);

foreach ($collPermissaoModulo as $oPermissaoModulo) { /* @var $oPermissaoModulo PermissaoModulo */
    if ($oPermissaoModulo->hasChildren()) {
        $moduleSession = $oPermissaoModulo->getNome();
        $options = array();
        foreach ($oPermissaoModulo->getChildren(PermissaoModuloQuery::create()->filterByMostrar(1)) as $oPermissaoSubModulo) { /* @var $oPermissaoSubModulo PermissaoModulo */
            $options[$oPermissaoSubModulo->getId()] = $oPermissaoSubModulo->getNome();
        }
    } else {
        $moduleSession = '&nbsp;';
        $options = array($oPermissaoModulo->getId() => $oPermissaoModulo->getNome());
    }
    $form->addElement(new Element\Checkbox($moduleSession, "data[MODULO_ID]", $options, array(
        "value" => $idPermissaoModuloGrupoSelected
    )));
}

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));


$form->render();
