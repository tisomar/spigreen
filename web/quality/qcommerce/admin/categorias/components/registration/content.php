<?php
/* @var $object Categoria */
use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

$form->addElement(new Element\Textbox("Nome:", "data[NOME]", array(
    "value" => $object->getNome(),
    "required" => true
)));

$categoriasList = array();
foreach ($root->getBranch($_classQuery::create()->filterByNrLvl(array('max' => 1))) as $categoria) {
    $nome = $categoria->isRoot() ? '--' : $categoria->getNome();
    $categoriasList[$categoria->getId()] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $categoria->getLevel()) . '' . $nome;
}

$form->addElement(new Element\Select("Subcategoria de:", "PARENT_ID", $categoriasList, array(
    "value" => $object->getParent() ? $object->getParent()->getId() : null,
)));

$form->addElement(new Element\Textbox("Ordem:", "data[ORDEM]", array(
    "value" => $object->getOrdem(),
    "required" => true
)));

$form->addElement(new Element\Textbox("URL:", "data[URL_CATEGORIA]", array(
    "value" => $object->getUrlCategoria(),
)));

$form->addElement(new Element\Radio("Categoria faz parte do KIT OURO?", "data[IS_CATEGORY_BY_KIT]", array(1 => 'Sim', 0 => 'Não'), array(
    "value" => $object->getIsCategoryByKit(),
    "required" => true,
    )));

    $form->addElement(new Element\Radio("Categoria de produtos combos?", "data[COMBO]", array(1 => 'Sim', 0 => 'Não'), array(
    "value" => $object->getCombo(),
    "required" => true,
    )));

    $form->addElement(new Element\Radio("Mostrar na Barra de Categorias?", "data[MOSTRAR_BARRA_MENU]", array(1 => 'Sim', 0 => 'Não'), array(
    "value" => $object->getMostrarBarraMenu(),
    "required" => true,
    )));

    $form->addElement(new Element\Radio("Mostrar na Página inicial?", "data[MOSTRAR_PAGINA_INICIAL]", array(1 => 'Sim', 0 => 'Não'), array(
    "value" => $object->getMostrarPaginaInicial(),
    "required" => true,
    )));

    $form->addElement(new Element\Radio("Mostrar no site?", "data[DISPONIVEL]", array(1 => 'Sim', 0 => 'Não'), array(
    "value" => $object->getDisponivel(),
    "required" => true,
    )));

    $form->addElement(new Element\FileImage("Banner", "BANNER", array(
    "shortDesc" => "Adicione imagem na dimensão <b>1140x114 pixels</b> para obter uma melhor visualização.",
    "dimensions" => array(
        'width' => '500px',
        'height' => '50px',
    ),
    'class' => 'check-ratio',
    "data-ratio" => "10",
    )));

    if ($object->isImagemExists()) {
        $form->addElement(new Element\Checkbox('Remover imagem', 'data', array('1' => 'Sim'), array('onclick' => '$("#img-banner-hidden").val("");')));
        $form->addElement(new Element\Hidden('data[BANNER]', $object->getBanner(), array('id' => 'img-banner-hidden')));

        $html = '
        <div class="form-group">
            <label class="col-sm-3 control-label" for="registrer-element-1">
            Imagem atual:</label>
            <div class="col-sm-6">
                ' . $object->getThumb('width=500&height=50&cropratio=10:1', array(
            'class' => 'thumbnail img-responsive',
            'style' => 'background: #555',
        )) . '
            </div>
        </div>';

        $form->addElement(new Element\HTML($html));
    }


    $form->addElement(new Element\SaveButton());
    $form->addElement(new Element\CancelButton($config['routes']['list']));

    if ($object->isNew() == false) {
        $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
    }

    $form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));


    $form->render();
