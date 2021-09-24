<?php

if (!isset($_class)) {
    trigger_error('você deve definir a classe $_class');
}

require_once QCOMMERCE_DIR . '/admin/includes/config.inc.php';
require_once QCOMMERCE_DIR . '/admin/includes/security.inc.php';

$root = $_classQuery::create()->findRoot();
if (is_null($root)) {
    $root = new $_class();
    $root->setNome('Categoria');
    $root->makeRoot(); // make this node the root of the tree
    $root->save();
}

$erros = array();

$_classPeer = $_class::PEER;

if ($request->query->has('id')) {
    $object = $_classPeer::retrieveByPK($request->query->get('id'));
} else {
    $object = new $_class();
}

if ($request->getMethod() == 'POST') {
    $parent = $_classPeer::retrieveByPK($request->request->get('PARENT_ID'));

    $data = trata_post_array($request->request->get('data'));

    $object->setByArray($data);

    /* @var $object Banner */
    $fieldsFunc = $object->getPeer()->getFieldNames();
    $fieldName = $object->getPeer()->getFieldNames(BasePeer::TYPE_FIELDNAME);

    /* @var $uploadedFile UploadedFile */
    foreach ($request->files->all() as $name => $uploadedFile) {
        if (is_null($uploadedFile)) {
            continue;
        }

        if ($uploadedFile->getError()) {
            $erros[] = $uploadedFile->getErrorMessage();
            continue;
        }

        $fileUploader = QPress\Upload\FileUploader\FileUploader::getInstance()
            ->setAllowedExtensions($object->allowedExtentions)
            ->setUploadDir($object->strPathImg)
            ->prepare($uploadedFile)
        ;

        if (false !== $file = $fileUploader->move($object->randomName)) {
            $key = array_search($name, $fieldName);
            $object->strPhpNameImagem = $fieldsFunc[$key];
            $object->deleteImagem();
            $object->_setImagem($file->getFileName());
        }

        if ($fileUploader->hasErrors()) {
            $erros = array_merge($erros, $fileUploader->getErrors());
        }
    }

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
        if ($request->request->get('redirectToOnSuccess') != '') {
            redirectTo($request->request->get('redirectToOnSuccess'));
        } else {
            redirectTo($config['routes']['list']);
        }
        exit; // -----------------------
    }

    if (count($erros)) {
        foreach ($erros as $erro) {
            $session->getFlashBag()->add('error', $erro);
        }
    }
}
