<?php

if (!isset($_class)) {
    trigger_error('você deve definir a classe $_class');
}

$_classPeer = $_class::PEER;

$erros = array();

if ($request->getMethod() == 'POST') {
    $data = ($request->request->get('data'));
    
    if (!isset($data['REGISTRO_ID'])) {
        $data['REGISTRO_ID'] = null;
    }
    
    $object = SeoQuery::create()
            ->filterByPagina($data['PAGINA'])
            ->filterByRegistroId($data['REGISTRO_ID'])
        ->findOneOrCreate();
    
    $object->setByArray($data);
    
    if ($object->myValidate($erros) && !$erros) {
        $object->save();
        $session->getFlashBag()->add('success', 'Registro armazenado com sucesso!');
    }

    if (count($erros)) {
        foreach ($erros as $erro) {
            $session->getFlashBag()->add('error', $erro);
        }
    }
} else {
    $object = SeoQuery::create()
            ->filterByPagina(SeoPeer::PAGINA_HOME)
        ->findOneOrCreate();
}
