<?php
if (!isset($_class))
{
    trigger_error('voc� deve definir a classe $_class');
}

use Symfony\Component\HttpFoundation\File\UploadedFile;

$erros = array();

$_classPeer = $_class::PEER;

if ($request->query->has('id'))
{
    $object = $_classPeer::retrieveByPK($request->query->get('id'));
}
else
{
    $object = new $_class();
}

if (!$object) {
    redirect_404admin();
}

if ($request->getMethod() == 'POST')
{

    $data = ($request->request->get('data'));

    $arrPermiteCliente = array(
        CupomPeer::CUPOM_MODEL_COSTUMERS => 'Cliente(s)',
        CupomPeer::CUPOM_MODEL_COSTUMERS_CHILDRENS => 'Filhos dos Cliente(s)',
        CupomPeer::CUPOM_MODEL_COSTUMERS_AND_CHILDRENS => 'Cliente(s) e seus Filhos',
    );

    if(isset($arrPermiteCliente[$data['MODELO_CLIENTE_USO']])){
        $data['CLIENTES'] = join(',', $request->request->get('CLIENTES'));
    } else {
        $data['CLIENTES'] = '';
    }

    $object->setByArray($data);
    /* @var $uploadedFile UploadedFile */
    foreach ($request->files->all() as $name => $uploadedFile)
    {
        if (is_null($uploadedFile))
        {
            continue;
        }

        $fileUploader = QPress\Upload\FileUploader\FileUploader::getInstance()
            ->setAllowedExtensions($object->allowedExtentions)
            ->setUploadDir($object->strPathImg)
            ->prepare($uploadedFile)
        ;

        if (false !== $file = $fileUploader->move())
        {
            $object->deleteImagem();
            $object->_setImagem($file->getFileName());
        }

        if ($fileUploader->hasErrors())
        {
            $erros = array_merge($erros, $fileUploader->getErrors());
        }
    }

    if ($object->myValidate($erros) && !$erros)
    {
        $object->save();

        $session->getFlashBag()->add('success', 'Registro armazenado com sucesso!');

        if ($request->request->get('redirectToOnSuccess') != '')
        {
            redirectTo($request->request->get('redirectToOnSuccess'));
        }
        else
        {
            redirectTo($config['routes']['list']);
        }
        exit; // -----------------------
    }

    if (count($erros))
    {
        foreach ($erros as $erro)
        {
            $session->getFlashBag()->add('error', $erro);
        }
    }
}