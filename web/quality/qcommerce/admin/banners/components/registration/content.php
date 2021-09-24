<?php
/* @var $object Rede */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));


$form->addElement(new Element\Textbox("Título:", "data[TITULO]", array(
    "value" => $object->getTitulo(),
    "required" => true
)));

$form->addElement(new Element\Number("Ordem", "data[ORDEM]", array(
    "value" => $object->getOrdem(),
    "required" => true,
    "min" => 1,
)));

$form->addElement(new Element\Radio("Ativo:", "data[MOSTRAR]", BannerPeer::getMostrarList(), array(
    "value" => $object->getMostrar(),
    "required" => true
)));


$dim = BannerPeer::getDimensaoList();
$options = array();
foreach (BannerPeer::getTipoList() as $tipo => $desc) {
    $options[$tipo] = $desc;
}

$table = '
    <hr>
    <div class="alert">
        <h4>Dimensões. <small>Para cadastrar um banner, você deverá consultar a tabela abaixo
        para descobrir a dimensão ideal para cada resolução.
        </small>
        </h4>

        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>Extra small devices<br>Phones (<768px)</th>
                    <th>Small devices<br>Tablets (≥768px)</th>
                    <th>Medium devices<br>Desktops (≥992px)</th>
                    <th>Large devices<br>Desktops (≥1200px)</th>
                </tr>
            </thead>
            <tbody>
';

if (Config::get('banner_full') == '1') {
    $table .= '
                    <tr>
                        <td>Banner Principal</td>
                        <td><span class="text-muted">não necessário</span></td>
                        <td>768x384 pixels (2:1)</td>
                        <td>1200x400 pixels (3:1)</td>
                        <td>1920x480 pixels (4:1)</td>
                    </tr>
                ';
} else {
    $table .= '
                     <tr>
                        <td>Banner Principal</td>
                        <td><span class="text-muted">não necessário</span></td>
                        <td>720x360 pixels (2:1)</td>
                        <td>940x313 pixels (3:1)</td>
                        <td>1140x380 pixels (3:1)</td>
                    </tr>
                ';
}

$table .= '
                <tr>
                        <td>Banner Apoio</td>
                        <td><span class="text-muted">não necessário</span></td>
                        <td><span class="text-muted">não necessário</span></td>
                        <td>390x260 pixels (1.5:1)</td>
                        <td><span class="text-muted">não necessário</span></td>
                        
                    </tr>
                    
                    
                <tr>
                        <td>Banner Rodapé</td>
                        <td><span class="text-muted">não necessário</span></td>
                        <td><span class="text-muted">não necessário</span></td>
                        <td>390x260 pixels (1.5:1)</td>
                        <td><span class="text-muted">não necessário</span></td>
                        
                </tr>

                <tr>
                    <td>Banner de Vantagem</td>
                    <td><span class="text-muted">não necessário</span></td>
                    <td><span class="text-muted">não necessário</span></td>
                    <td>1140x89 pixels</td>
                    <td><span class="text-muted">não necessário</span></td>
                </tr>

            </tbody>
        </table>

    </div>
';
$form->addElement(new Element\HTML($table));

$form->addElement(new Element\Radio("Tipo:", "data[TIPO]", $options, array(
    "required" => true,
    "value" => $object->getTipo(),
)));

/*$form->addElement(new Element\FileImage("Extra small devices<br>Phones (<768px)", "IMAGEM_XS", array(
    "dimensions" => array(
        'width' => '100%',
        'height' => 'auto',
    )
)));
if ($object->getImagemXs() != "") {
    $form->addElement(new Element\Hidden('data[IMAGEM_XS]', $object->getImagemXs()));
    $html = '
        <div class="form-group">
            <label class="col-sm-3 control-label" for="registrer-element-1">
            Extra small devices<br>Phones (<768px)<br>Imagem atual:</label>
            <div class="col-sm-6">
                ' . $object->setStrImagem('ImagemXs')->getThumb('width=250&height=300', array('class' => 'thumbnail','style' => 'background: #555',)) . '
            </div>
        </div>';
    $form->addElement(new Element\HTML($html));
}*/

// CARREGAR IMAGEM
function PegaImagem($arquivo) {
    if ($arquivo != ""){
        $img = asset('/arquivos/banners/'). $arquivo;
    }else{
        $img = asset('/arquivos/banners/default.png');
    }
    return $img;
}

$form->addElement(new Element\FileImage("Small devices<br>Tablets (≥768px)", "IMAGEM_SM", array(
    "dimensions" => array(
        'width' => '100%',
        'height' => 'auto',
    )
)));
if ($object->getImagemSm() != "") {
    $form->addElement(new Element\Hidden('data[IMAGEM_SM]', $object->getImagemSm()));
    $html = '
        <div class="form-group">
            <label class="col-sm-3 control-label" for="registrer-element-1">
            Small devices<br>Tablets (≥768px)<br>Imagem atual:</label>
            <div class="col-sm-6">
                <img style="width:62%" src="'. PegaImagem($object->getImagemSm()) . '" alt="" >
            </div>
        </div>';
    $form->addElement(new Element\HTML($html));
}



$form->addElement(new Element\FileImage("Medium devices<br>Desktops (≥992px)", "IMAGEM_MD", array(
    "dimensions" => array(
        'width' => '100%',
        'height' => 'auto',
    )
)));
if ($object->getImagemMd() != "") {
    $form->addElement(new Element\Hidden('data[IMAGEM_MD]', $object->getImagemMd()));
    $html = '
        <div class="form-group">
            <label class="col-sm-3 control-label" for="registrer-element-1">
            Medium devices<br>Desktops (≥992px)<br>Imagem atual:</label>
            <div class="col-sm-6">
                <img style="width:62%" src="'. PegaImagem($object->getImagemMd()) . '" alt="" >
            </div>
        </div>';
    $form->addElement(new Element\HTML($html));
}

$form->addElement(new Element\FileImage("Large devices<br>Desktops (≥1200px)", "IMAGEM_LG", array(
    "dimensions" => array(
        'width' => '100%',
        'height' => 'auto',
    )
)));
if ($object->getImagemLg() != "") {
    $form->addElement(new Element\Hidden('data[IMAGEM_LG]', $object->getImagemLg()));
    $html = '
        <div class="form-group">
            <label class="col-sm-3 control-label" for="registrer-element-1">
            Large devices<br>Desktops (≥1200px)<br>Imagem atual:</label>
            <div class="col-sm-6">
                <img style="width:62%" src="'. PegaImagem($object->getImagemLg()) . '" alt="" >
            </div>
        </div>';
    $form->addElement(new Element\HTML($html));
}


$form->addElement(new Element\Textbox("Link:", "data[LINK]", array(
    "value" => $object->getLink(),
    "placeholder" => 'Ex.: http://www.qualitypress.com.br/solucoes/detalhes/qcommerce'
)));

$form->addElement(new Element\Radio("Abrir em:", "data[TARGET]", BannerPeer::getTargetList(), array(
    "value" => $object->getTarget(),
)));


$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));

$form->render();
