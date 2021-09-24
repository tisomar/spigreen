<?php
if (!isset($_class)) :
    trigger_error('você deve definir a classe $_class');
endif;

require_once QCOMMERCE_DIR . '/admin/includes/config.inc.php';
require_once QCOMMERCE_DIR . '/admin/includes/security.inc.php';

$erros = array();

$_classPeer = $_class::PEER;

if ($request->query->has('id')) :
    $object = $_classPeer::retrieveByPK($request->query->get('id'));
else :
    $object = new $_class();
endif;

// Caso a opção cliente(s) específico(s) seja a selecionada
$clienteQuery = ClienteQuery::create()
    ->where('Cliente.Id IN ?', explode(',', $object->getIdClientesStr()))
    ->find();

$clientedIds = array();
$clientesNomes = array();

/**
 * @var $cliente Cliente
 */
foreach ($clienteQuery as $cliente) :
    $clientedIds[] = $cliente->getId();
    $clientesNomes[] = $cliente->getNome();
endforeach;

if ($request->getMethod() == 'POST') :
    /** @var DocumentoAlerta $object */

    $data = trata_post_array($request->request->get('data'));

    $object->setByArray($data);
    $object->setUsuario(UsuarioPeer::getUsuarioLogado());

    if ($data['TIPO_MENSAGEM'] == 'aniversariantes') :
        $object->setSomenteLeitura(true);
    endif;

    switch ($data['TIPO_DEST']) :
        case 'todos':
            $clientesIds = ClientePeer::getAllClientsIds();
            $object->setIdClientesStr(',' . $clientesIds . ',');
            break;
        case 'combo':
            $clientesComboIds = ClientePeer::getClientsIdsByCombo(' IS NOT NULL ');
            $object->setIdClientesStr(',' . $clientesComboIds->CLIENTES . ',');
            break;
        case 'not_combo':
            $clientesComboIds = ClientePeer::getClientsIdsByCombo(' IS NULL ');
            $object->setIdClientesStr(',' . $clientesComboIds->CLIENTES . ',');
            break;
        case 'ativo':
            $clientesAtivoIds = ClientePeer::getClientsIdsByAtivoMes('1');
            $object->setIdClientesStr(',' . $clientesAtivoIds->CLIENTES . ',');
            break;
        case 'not_ativo':
            $clientesAtivoIds = ClientePeer::getClientsIdsByAtivoMes('0');
            $object->setIdClientesStr(',' . $clientesAtivoIds->CLIENTES . ',');
            break;
        case 'nivel_mensal':
            $erros[] = 'A mensagem não tem destinatários';
            break;
        case 'cliente':
            $dataStrCli = $request->request->get('CLIENTES_ID');

            if (!is_null($dataStrCli) && is_array($dataStrCli)) :
                $cli = '';
                foreach ($dataStrCli as $val) :
                    $cli .= $val . ',';
                endforeach;
                $object->setIdClientesStr(',' . $cli);
            else :
                $erros[] = 'A mensagem não tem destinatários';
            endif;
            break;
    endswitch;

    /* @var $object Banner */
    $fieldsFunc = $object->getPeer()->getFieldNames();
    $fieldName = $object->getPeer()->getFieldNames(BasePeer::TYPE_FIELDNAME);

    /* @var $uploadedFile UploadedFile */
    foreach ($request->files->all() as $name => $uploadedFile) :
        if (is_null($uploadedFile)) :
            continue;
        endif;

        if ($uploadedFile->getError()) :
            $erros[] = $uploadedFile->getErrorMessage();
            continue;
        endif;

        $fileUploader = QPress\Upload\FileUploader\FileUploader::getInstance()
            ->setAllowedExtensions($object->allowedExtentions)
            ->setUploadDir($object->strPathImg)
            ->prepare($uploadedFile)
        ;

        if (false !== $file = $fileUploader->move($object->randomName)) :
            $key = array_search($name, $fieldName);
            $object->strPhpNameImagem = $fieldsFunc[$key];
            $object->deleteImagem();
            $object->_setImagem($file->getFileName());
        endif;

        if ($fileUploader->hasErrors()) :
            $erros = array_merge($erros, $fileUploader->getErrors());
        endif;
    endforeach;

    if ($object->myValidate($erros) && !$erros) :
        $object->save();

        $session->getFlashBag()->add('success', 'Registro armazenado com sucesso!');

        if ($request->request->get('redirectToOnSuccess') != '') :
            redirectTo($request->request->get('redirectToOnSuccess'));
        else :
            redirectTo($config['routes']['list']);
        endif;
        exit; // -----------------------
    endif;

    if (count($erros)) :
        foreach ($erros as $erro) :
            $session->getFlashBag()->add('error', $erro);
        endforeach;
    endif;
endif;
