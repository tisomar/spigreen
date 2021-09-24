<?php
use Symfony\Component\HttpFoundation\File\UploadedFile;

if (!isset($_class)) {
    trigger_error('você deve definir a classe $_class');
}

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

if ($request->getMethod() == 'POST') {
    $data = ($request->request->get('data'));
    
    $fieldsFunc = $object->getPeer()->getFieldNames();
    $fieldName = $object->getPeer()->getFieldNames(BasePeer::TYPE_FIELDNAME);

    /* @var $uploadedFile UploadedFile */
    foreach ($request->files->all() as $name => $uploadedFile) {
    
        if(!empty($request->files->get($name))) :

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
            
            if($name == 'bannerGraduacao') :
                $data['BANNER_GRADUACAO'] = $file->getFilename();
            else:
                $data['IMAGEM'] = $file->getFilename();
            endif;
        
            if ($fileUploader->hasErrors()) {
                $erros = array_merge($erros, $fileUploader->getErrors());
            }
        endif;
    }

    $object->setByArray($data);
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
