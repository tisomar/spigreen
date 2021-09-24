<?php
//header('Content-Type: application/json');

require_once QCOMMERCE_DIR . '/admin/includes/config.inc.php';
require_once QCOMMERCE_DIR . '/admin/includes/security.inc.php';

use Symfony\Component\HttpFoundation\File\UploadedFile;
use QPress\Upload\FileUploader\FileUploader;

$id = $request->query->get('id');

$erros = array();
$files = array();

$urlOrdem   = get_url_admin() . '/ajax/save-data/?model=GaleriaArquivo&method=Ordem';
$urlLegenda = get_url_admin() . '/ajax/save-data/?model=GaleriaArquivo&method=Legenda';

if ($request->getMethod() == 'POST') {
    $object = new GaleriaArquivo();
    $object->setGaleriaId($id);

    $f = $request->files->get('files');

    /* @var $uploadedFile UploadedFile */
    $uploadedFile = $f[0];

    if (is_null($uploadedFile)) {
        $files = array(
            "error" => "Arquivo não encontrado!"
        );
    } elseif ($uploadedFile->getError()) {
        $files = array(
            "error" => $uploadedFile->getErrorMessage()
        );
    } else {
        $search = sprintf('.%s', $uploadedFile->getClientOriginalExtension());
        $legenda = str_replace($search, '', $uploadedFile->getClientOriginalName());

        $fileUploader = FileUploader::getInstance()
            ->setMaxAllowedSize(1024 * 1024)
            ->setAllowedExtensions($object->allowedExtentions)
            ->setUploadDir($object->strPathImg)
            ->prepare($uploadedFile);

        if (false !== $file = $fileUploader->move()) {
            $ordem = GaleriaArquivoQuery::create()
                ->withColumn('MAX(GaleriaArquivo.Ordem)', 'Ordem')
                ->select(array('Ordem'))
                ->filterByGaleriaId($id)
                ->groupByGaleriaId()
                ->findOne();

            $ordem++;

            $object->_setImagem($file->getFileName());
            $object->setLegenda($legenda);
            $object->setOrdem($ordem);
        }

        if ($fileUploader->hasErrors()) {
            $erros = array_merge($erros, $fileUploader->getErrors());
        }

        if ($object->myValidate($erros) && !$erros) {
            $con = Propel::getConnection();
            $con->beginTransaction();

            try {
                $object->save();

                $files[0] = array(
                    'id'            => $object->getId(),
                    'name'          => $object->_getImagem(),
                    'size'          => @filesize($object->_getAbsolutePathImagem() . $object->_getImagem()),
                    'url'           => $object->getUrlImageResize(''),
                    'thumbnailUrl'  => $object->getUrlImageResize('width=100&height=100'),
                    'deleteUrl'     => delete('GaleriaArquivo', $object->getId()),
                    'deleteType'    => 'GET',

                    // editables informations
                    'urlLegenda'    => $urlLegenda,
                    'legenda'       => !is_null($object->getLegenda()) ? $object->getLegenda() : '',
                    'urlOrdem'      => $urlOrdem,
                    'ordem'         => $object->getOrdem(),

                );

                $con->commit();
            } catch (Exception $e) {
                $con->rollBack();
                $files[0]['error'] = 'Não foi possível enviar a imagem. Provavelmente ela deve estar corrompida.';
            }
        }
    }
} else {
    $fotos = GaleriaArquivoQuery::create()->filterByGaleriaId($id)->orderByOrdem()->find();

    $files = array();

    /* @var $object GaleriaArquivo */
    foreach ($fotos as $object) {
        $files[] = array (

            'id'            => $object->getId(),
            'name'          => $object->_getImagem(),
            'size'          => @filesize($object->_getAbsolutePathImagem() . $object->_getImagem()),
            'url'           => $object->getUrlImageResize(''),
            'thumbnailUrl'  => $object->getUrlImageResize('width=100&height=100'),
            'deleteUrl'     => delete('GaleriaArquivo', $object->getId()),
            'deleteType'    => 'GET',

            // editables informations
            'urlLegenda'    => $urlLegenda,
            'legenda'       => !is_null($object->getLegenda()) ? $object->getLegenda() : '',
            'urlOrdem'      => $urlOrdem,
            'ordem'         => $object->getOrdem(),
        );
    }
}

echo json_encode(array('files' => $files));
