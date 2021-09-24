<?php
include __DIR__ . '/../../config/menu.php';

/* @var $object Produto */
/* @var $container \QPress\Container\Container */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

$form->addElement(new Element\Radio("Ativo?", "data[ATIVO]", array(1 => 'Sim', 0 => 'Não'), array(
    "value" => $gShop->getAtivo(),
    'required' => true
)));


$formhtml = '
    <div class="form-group">
        <label class="col-sm-3 control-label">
            <span class="required">* </span>
            Categoria no Google
        </label>
        <div class="col-sm-6">
            <input type="hidden" name="data[GCATEGORY]" id="GCATEGORY" class="form-control" required="required"
                    value="' . $gShop->getGoogleShoppingCategoria() . '" />
            
            <div class="col-sm-12">
                <span class="help-block">
                <b>Categoria atual</b><br>
                ' . (   !is_null($gShop->getGoogleShoppingCategoria())
                        ? $gShop->getGoogleShoppingCategoria()->getNome()
                        : 'Selecione uma categoria do google'
                    ) . '
                </span>
            </div>
        </div>
    </div>';
$form->addElement(new Element\HTML($formhtml));


$form->addElement(new Element\Select("Faixa Etária:", "data[FAIXA_ETARIA]", GoogleShoppingItemPeer::getFaixaEtariaOptions(), array(
    "value" => $gShop->getFaixaEtaria()
)));

$form->addElement(new Element\Select("Genero:", "data[GENERO]", GoogleShoppingItemPeer::getGeneroOptions(), array(
    "value" => $gShop->getGenero()
)));

$form->addElement(new Element\Select("Condição:", "data[CONDICAO]", GoogleShoppingItemPeer::getCondicaoOptions(), array(
    "value" => $gShop->getCondicao()
)));

$form->addElement(new Element\Radio("Utilização de imagens:", "data[USAR_IMAGENS]", array(1 => 'Todas', 0 => 'Somente uma'), array(
    "value" => $gShop->getUsarImagens(),
)));

$form->addElement(new Element\Select("Somente para adultos (18+)?", "data[ADULTO]", GoogleShoppingItemPeer::getAdultoOptions(), array(
    "value" => $gShop->getAdulto()
)));

$form->addElement(new Element\Hidden('data[PRODUTO_ID]', $_GET['reference']));

$form->addElement(new Element\SaveButton());

$form->render();

?>
<script>
    $(function() {
        $("#GCATEGORY").select2({
            placeholder: "Selecione uma categoria do google",
            width: 'resolve',
            minimumInputLength: 2,
            ajax: {
                url: window.root_path + '/admin/google-shopping/gs_category',
                dataType: 'json',
                data: function (term, page) {
                    return {
                        q: term
                    };
                },
                results: function (data, page) {
                    return { results: data };
                }
            }
        });
    });
</script>
