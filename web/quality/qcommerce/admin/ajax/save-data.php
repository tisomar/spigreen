<?php

if (!isset($_GET['model'])) {
    throw new Exception('parâmetro \'model\' não informado!');
} elseif (is_empty($_GET['model']) || !class_exists($_GET['model'])) {
    throw new Exception(sprintf('model %s não encontrado!', $_GET['model']));
}

$model = $_GET['model'];
$peer = $model . 'Peer';

$object = $peer::retrieveByPk($_POST['pk']);

if (is_null($object)) {
    die('Objeto não encontrado!');
}

### Verificar se os valores são enviados por array
if (isset($_POST['value']) && is_array($_POST['value'])) {
    $_POST['value'] = json_encode($_POST['value']);
}

### Caso não tenha postado valores, provavelmente é derivado do checklist
if (!isset($_POST['value'])) {
    $_POST['value'] = json_encode(array());
}

### Evento preSave() para o alias específicos
if (method_exists($object, 'getAlias') && true) {
    $methodName =  'pre_save_' . str_replace('.', '_', $object->getAlias());
    if (method_exists($object, $methodName)) {
        call_user_func(array($object, $methodName));
    }
}

$method = 'set' . $_GET['method'];
$object->$method($_POST['value']);

if($object instanceof Pedido) :
    $object->avisaClienteComCodigosFrete($method);
endif;

### Salvar o objeto
$object->save();

### Carrega os valores dos parametros em memória para que se algum recurso precisar nesta requisição.
$container->getConfig()->loadParameters();

### Evento postSave() para o alias específicos
if (method_exists($object, 'getAlias')) {
    $methodName =  'post_save_' . str_replace('.', '_', $object->getAlias());
    if (method_exists($object, $methodName)) {
        call_user_func(array($object, $methodName));
    }
}
