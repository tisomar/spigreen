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

$form->addElement(new Element\Email("E-mail:", "data[EMAIL]", array(
    "value" => $object->getEmail(),
    "required" => true
)));

$form->addElement(new Element\Textbox("Login:", "data[LOGIN]", array(
    "value" => $object->getLogin(),
    "required" => true
)));

$options = $object->isNew() ? array('required' => true) : array('shortDesc' => 'Deixe em branco para não alterar');
$options = array_merge(array('id' => 'senha'), $options);
$form->addElement(new Element\Password("Senha:", "data[SENHA]", $options));

$idGruposSelecionados = PermissaoGrupoUsuarioQuery::create()->select(array('GrupoId'))->filterByUsuarioId($object->getId())->find()->toArray();

$aGrupos = PermissaoGrupoQuery::create()->select(array('Id', 'Nome'))->orderByNome()->find()->toArray();
$options = array();
foreach ($aGrupos as $aGrupo) {
    $options[$aGrupo['Id']] = $aGrupo['Nome'];
}

$form->addElement(new Element\Checkbox("Grupo:", "data[GRUPO_ID]", $options, array(
    "value" => $idGruposSelecionados,
)));



$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));

$form->render();
