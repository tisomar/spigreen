<?php
header('Content-Type: application/json');

require_once QCOMMERCE_DIR . '/admin/includes/config.inc.php';
require_once QCOMMERCE_DIR . '/admin/includes/security.inc.php';

use Symfony\Component\HttpFoundation\File\UploadedFile;

$reference = $request->query->get('reference');

$erros = array();

$urlOrder = get_url_admin() . '/ajax/save-data/?model=Foto&method=Ordem';
$urlLegenda = get_url_admin() . '/ajax/save-data/?model=Foto&method=Legenda';
$urlCor = get_url_admin() . '/ajax/save-data/?model=Foto&method=Cor';

$cores = ProdutoVariacaoAtributoQuery::create()
    ->useProdutoAtributoQuery()
    ->filterByProdutoId($reference)
    ->filterByType(ProdutoAtributoPeer::TYPE_COR)
    ->endUse()
    ->groupByDescricao()
    ->find();

$options = array('' => '');
foreach ($cores as $cor) {
    $options[$cor->getDescricao()] = $cor->getDescricao();
}
$optionsCor = str_replace('"', "'", json_encode($options));

if ($request->getMethod() == 'POST') {
    $object = new Foto();
    $object->setProdutoId($reference);

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
        $fileUploader = QPress\Upload\FileUploader\FileUploader::getInstance()
            ->setMaxAllowedSize(1024 * 1024)
            ->setAllowedExtensions($object->allowedExtentions)
            ->setUploadDir($object->strPathImg)
            ->prepare($uploadedFile);

        if (false !== $file = $fileUploader->move()) {
            $object->_setImagem($file->getFileName());
        }

        if ($fileUploader->hasErrors()) {
            $erros = array_merge($erros, $fileUploader->getErrors());
        }



        if ($object->myValidate($erros) && !$erros) {
            $con = Propel::getConnection();
            $con->beginTransaction();
            try {
                $object->save($con);

                $files[0] = array(
                    'hasCor' => Config::get('has_produto_cor'),
                    'urlOrder' => $urlOrder,
                    'urlLegenda' => $urlLegenda,
                    'urlCor' => $urlCor,
                    'legenda' => !is_null($object->getLegenda()) ? $object->getLegenda() : '',
                    'cor' => !is_null($object->getCor()) ? $object->getCor() : '',
                    'optionsCor' => $optionsCor,
                    'id' => $object->getId(),
                    'order' => $object->getOrdem(),
                    'name' => $object->getImagem(),
                    'size' => @filesize($object->_getAbsolutePathImagem() . $object->getImagem()),
                    'url' => $object->getUrlImageResize(''),
                    'thumbnailUrl' => $object->getUrlImageResize('width=150&height=150'),
                    'deleteUrl' => delete('Foto', $object->getId()),
                    'deleteType' => 'GET',
                );

                $con->commit();
            } catch (Exception $e) {
                $con->rollBack();
                $files[0]['error'] = 'Não foi possível enviar a imagem. Provavelmente ela deve estar corrompida.';
            }
        }
    }
} else {
    $fotos = FotoQuery::create()->orderByOrdem()->orderByCor()->filterByProdutoId($reference)->find();

    $files = array();

    foreach ($fotos as $object) { /* @var $object Foto */
        $files[] = array (

            'hasCor' => Config::get('has_produto_cor'),

            'urlOrder' => $urlOrder,
            'urlLegenda' => $urlLegenda,
            'urlCor' => $urlCor,

            'legenda' => !is_null($object->getLegenda()) ? $object->getLegenda() : '',
            'cor' => !is_null($object->getCor()) ? $object->getCor() : '',

            'optionsCor' => $optionsCor,

            'id' => $object->getId(),
            'order' => $object->getOrdem(),

            'name' => $object->getImagem(),
            'size' => @filesize($object->_getAbsolutePathImagem() . $object->getImagem()),
            'url' => $object->getUrlImageResize(''),
            'thumbnailUrl' => $object->getUrlImageResize('width=150&height=150'),
            'deleteUrl' => delete('Foto', $object->getId()),
            'deleteType' => 'GET',
        );
    }
}

echo json_encode(array('files' => $files));
