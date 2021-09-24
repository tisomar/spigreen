<?php
/* @var $object EstoqueProduto */
if (!isset($_class)) {
    trigger_error('você deve definir a classe $_class');
}

use Symfony\Component\HttpFoundation\File\UploadedFile;

$erros = array();

$_classPeer = $_class::PEER;

$object = new $_class();

if ($container->getRequest()->query->has('produto_variacao_id')) {
    $_SESSION['PRODUTO_VARIACAO_ESTOQUE'] = $container->getRequest()->query->get('produto_variacao_id');
}

if (!$object) {
    redirect_404admin();
}

if ($request->getMethod() == 'POST') {
    $data = ($request->request->get('data'));
    $data['PRODUTO_ID'] = $container->getRequest()->query->get('produto_id') ?? $data['PRODUTO_ID'];
    $userLogado = UsuarioPeer::getUsuarioLogado()->getNome();

    $avisoUserAlteracao = 'Estoque alterado pelo usuário: ' . $userLogado;
    $observacao = strlen($data['OBSERVACAO']) > 1 ? $data['OBSERVACAO'] . "<br><br>{$avisoUserAlteracao}" :  $avisoUserAlteracao;
    $data['OBSERVACAO'] =  $observacao;

    $object->setByArray($data);
    $object->setData(date('Y-m-d H:i:s'));

    /* @var $object EstoqueProduto */
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

    if ($object->myValidate($erros) && !$erros) {
        $object->save();

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
