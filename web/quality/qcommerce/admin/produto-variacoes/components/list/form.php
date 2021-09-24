<div class="well well-sm">
    Informe as variações de acordo com cada atributo adicionado. 
    Você pode adicionar os itens separados por vírgula (,).<br />
    <b>Ex.: P, M, G ou Preto, Branco, Verde ...</b>
</div>

<?php
/* @var $product Produto */

use PFBC\Form;
use PFBC\Element;

$form = new Form("form-cadastro");

$form->configure(array(
    'action' => $container->getRequest()->server->get('REQUEST_URI')
));

foreach ($arrProdutoAtributos as $iAtributo => $objProdutoAtributo) {
    if ($objProdutoAtributo->isCor()) {
        $options = array();

        $coll = ProdutoCorQuery::create()
                ->orderByNome()
                ->find()
                ->toArray();

        $form->addElement(new Element\Select("<b>" . $objProdutoAtributo->getDescricao() . ":</b>", "atributo_valor[" . $objProdutoAtributo->getId() . "]", array_column($coll, 'Nome', 'Id'), array(
            "title" => "Você pode digitar todas as opções deste atributo separando-os por virgula (,), desta forma você gerará várias variações ao mesmo tempo. "
            . "Caso queira gerar apenas uma variação digite apenas um nome no campo.",
            'required' => 'required',
            'class' => 'select2',
            'multiple' => true,
        )));
    } else {
        $form->addElement(new Element\Textbox("<b>" . $objProdutoAtributo->getDescricao() . ":</b>", "atributo_valor[" . $objProdutoAtributo->getId() . "]", array(
            "title" => "Você pode digitar todas as opções deste atributo separando-os por virgula (,), desta forma você gerará várias variações ao mesmo tempo. "
            . "Caso queira gerar apenas uma variação digite apenas um nome no campo.",
            'required' => 'required',
            'class' => 'input-token',
        )));
    }
    $form->addElement(new Element\Hidden("atributo_id[]", $objProdutoAtributo->getId()));
}

$form->addElement(new Element\HTML("<hr />"));
$form->addElement(new Element\HTML("<div class='well well-sm'><i class='icon-info-sign'></i> Os valores abaixo são os valores base do produto.</div>"));

$form->addElement(new Element\Textbox("Preço Normal:", "variacao[VALOR_BASE]", array(
    "class" => 'mask-money',
    "value" => 'R$ ' . format_number($product->getValorBase()),
    'required' => 'required'
)));

$form->addElement(new Element\Textbox("Preço de Oferta:", "variacao[VALOR_PROMOCIONAL]", array(
    "class" => 'mask-money',
    "value" => 'R$ ' . format_number($product->getValorPromocional()),
)));

$form->addElement(new Element\Number("Estoque Atual:", "variacao[ESTOQUE_ATUAL]", array(
    'value' => $product->getEstoqueAtual(),
    'required' => 'required'
)));

if (Config::get('aviso_estoque_minimo')) {
    $form->addElement(new Element\Number("Qtd mínima em estoque:", "variacao[ESTOQUE_MINIMO]", array(
        'value' => $product->getEstoqueMinimo(),
        'required' => 'required'
    )));
}
$form->addElement(new Element\Radio("Liberar para venda:", "variacao[DISPONIVEL]", ProdutoVariacaoPeer::getValueSet(ProdutoVariacaoPeer::DISPONIVEL), array(
    'value' => $product->getDisponivel(),
    'required' => true,
)));

$form->addElement(new Element\SaveButton("Gerar Variações"));


$form->addElement(new Element\Hidden("form", "registrer"));

$form->render();
