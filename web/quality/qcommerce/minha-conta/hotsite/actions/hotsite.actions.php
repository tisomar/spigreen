<?php

$cliente = ClientePeer::getClienteLogado(true);

$erros = array();
$objHotsite = HotsiteQuery::create()
    ->filterByCliente($cliente)
    ->findOne();

if (!$objHotsite instanceof Hotsite) {
    $objHotsite = new Hotsite();
}

if ($request->request->has('hotsite')) {
    $arrHotsite = $request->request->get('hotsite');
    $arrHotsite['SLUG'] = normalizaURL($arrHotsite['URL']);
    $objHotsite->setByArray($arrHotsite);
    $objHotsite->setCliente($cliente);
    
    /* @var $uploadedFile UploadedFile */
    foreach ($request->files->all() as $name => $uploadedFile) {
        if (is_null($uploadedFile['FOTO'])) {
            continue;
        }
        
        $fileUploader = QPress\Upload\FileUploader\FileUploader::getInstance()
            ->setMaxAllowedSize(1024 * 1024)
            ->setAllowedExtensions($objHotsite->allowedExtentions)
            ->setUploadDir($objHotsite->strPathImg)
            ->prepare($uploadedFile['FOTO'])
        ;
        
        if (false !== $file = $fileUploader->move()) {
            $objHotsite->deleteImagem();
            $objHotsite->_setImagem($file->getFileName());
        }
        
        if ($fileUploader->hasErrors()) {
            $erros = array_merge($erros, $fileUploader->getErrors());
        }
    }
    
    if ($objHotsite->myValidate($erros) && !$erros) {
        $objHotsite->save();
        
        FlashMsg::success('Hotsite configurado com sucesso.');
        
        redirect('/minha-conta/hotsite');
    }
}

foreach ($erros as $erro) {
    FlashMsg::danger($erro);
}


function normalizaURL($str)
{
    $str = strtolower(utf8_decode($str));
    $i = 1;
    $str = strtr($str, utf8_decode('àáâãäåæçèéêëìíîïñòóôõöøùúûýýÿ'), 'aaaaaaaceeeeiiiinoooooouuuyyy');
    $str = preg_replace("/([^a-z0-9])/", '-', utf8_encode($str));
    while ($i > 0) {
        $str = str_replace('--', '-', $str, $i);
    }
    if (substr($str, -1) == '-') {
        $str = substr($str, 0, -1);
    }
    return $str;
}
