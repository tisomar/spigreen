<?php
/* @var $object Cliente */
if (!isset($_class)) {
    trigger_error('você deve definir a classe $_class');
}

use Symfony\Component\HttpFoundation\File\UploadedFile;

$erros = array();

$_classPeer = $_class::PEER;

if ($request->query->has('id')) {
    $object = $_classPeer::retrieveByPK($request->query->get('id'));
} else {
    $object = new $_class();
}

if (!$object) {
    redirect_404admin();
}

$listCombos = PlanoQuery::create()->find();

if ($request->getMethod() == 'POST') {
    $data = ($request->request->get('data'));

    $oldPlanoId = $object->getPlanoId();

    $object->setByArray($data);

    if($data['REMOVE_CADASTRO_PJ'] == '1') :
        $usuario = UsuarioPeer::getUsuarioLogado();
        $usuario_id = $usuario->getId();

        $url = $_SERVER['REQUEST_URI'];

        $logAdmin = new LogAdmin();
        $logAdmin->setUsuarioId($usuario_id);
        $logAdmin->setData(date('Y-m-d H:i:s'));
        $logAdmin->setUrl($url);
        $logAdmin->setModulo('Edição de clientes');
        $logAdmin->setSql("\UPDATE `qp1_cliente` SET `RAZAO_SOCIAL`= null, `CNPJ`=null, `NOME_FANTASIA`=null, `INSCRICAO_ESTADUAL`=null WHERE qp1_cliente.ID={$object->getId()}");
        $logAdmin->setUpdatedAt(date('Y-m-d H:i:s'));
        $logAdmin->save();
        
        $object->setCnpj(null);
        $object->setRazaoSocial(null);
        $object->setNomeFantasia(null);
        $object->setInscricaoEstadual(null);
    endif;

    if ($oldPlanoId != $object->getPlanoId()) :
        $object->setDataAtivacao(new Datetime());
    endif;

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

    if ($object->myValidate($erros) && !$erros) {
        $object->save();

        $object->getFaturamentoDiretoClientes()->delete();
        $object->clearFaturamentoDiretoClientes();

        if (!is_null($container->getRequest()->request->get('FATURAMENTO_DIRETO'))) {
            foreach ($container->getRequest()->request->get('FATURAMENTO_DIRETO') as $faturamentoDiretoId) {
                $novoFaturamentoAssociado = new FaturamentoDiretoCliente();
                $novoFaturamentoAssociado->setFaturamentoDiretoId($faturamentoDiretoId);
                $object->addFaturamentoDiretoCliente($novoFaturamentoAssociado);
            }
            $object->save();
        }

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
