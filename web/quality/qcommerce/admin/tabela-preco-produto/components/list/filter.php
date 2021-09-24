<?php

use PFBC\Form;
use PFBC\Element;
use QPress\Template\Widget;

$form = new Form("form-filter");

$form->configure(array(
    'action' => $request->server->get('REQUEST_URI'),
    'method' => 'get',
));

$form->addElement(new Element\Select("<h5>Selecione a tabela</h5>", "id", $tabelas, array(
    "value" => $request->query->get('id', null, true),
    "class" => ' input-lg'
)));

$form->addElement(new Element\HTML('
    <div class="form-group">
        <label class="col-sm-3" for=""></label>
        <div class="col-sm-6">
            <a href="' . get_url_admin() . '/tabela-preco/registration/?id=' . $tabelaId . '" target="_blank">
                <i class="icon-external-link"></i>
                Acessar o cadastro desta tabela.
            </a>
        </div>
    </div>
'));

$form->addElement(new Element\FilterButton('Buscar produtos'));

$form->addElement(new Element\Hidden("page", ""));
$form->addElement(new Element\Hidden("is_filter", "true"));

Widget::render('admin/filter', $form->render(true));
