<?php
use PFBC\View;
use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

$form->addElement(new Element\Select("Tipo da operação:", "data[OPERACAO]", array('ENTRADA' => 'Adicionar estoque','SAIDA' => 'Retirar Estoque'), array(
    "value" => '',
)));

$arrProdutosSimples = ProdutoPeer::getProdutoSimplesList();

$form->addElement(new Element\Select("Produtos:", "data[PRODUTO_ID]", $arrProdutosSimples, array(
    "value" => $container->getRequest()->query->has('produto_id') ?
                $container->getRequest()->query->get('produto_id') : '',
)));

$form->addElement(new Element\Select("Variacao:", "data[PRODUTO_VARIACAO_ID]", array('' => ''), array(
    "value" => $container->getRequest()->query->has('produto_variacao_id') ?
                    $container->getRequest()->query->get('produto_variacao_id') : '',
    'id'    => 'produto-variacao',
)));

$form->addElement(new Element\HTML("<div class='form-group' id='produto-variacao-quantidade'></div>"));


$form->addElement(new Element\Number("Quantidade", "data[QUANTIDADE]", array(
    "value" => $object->getQuantidade(),
    "required" => true
)));

$aCentrosDistribuicao = CentroDistribuicaoPeer::getCentrosDistribuicaoList();

$form->addElement(new Element\Select("Centro de distribuição:", "data[CENTRO_DISTRIBUICAO_ID]", $aCentrosDistribuicao, array(
    "value" => $container->getRequest()->query->has('centro_distribuicao_id') ?
        $container->getRequest()->query->get('centro_distribuicao_id') : '',
    "required" => true
)));

$form->addElement(new Element\Textarea("Observação", "data[OBSERVACAO]", array(
    "value" => $object->getObservacao(),
)));

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

$form->addElement(new Element\HTML("
<script>
    function getListProductVariation(click){
        var value = $(click).val();
        if(value > 0){
            $.ajax({
                url: window.root_path + '/admin/ajax/getProdutoVariacaoByAddEstoque',
                type: 'POST',
                data: 'produto_id='+value,
                success: function(data){
                    var returned = $.parseJSON(data);
                    if(returned.retorno == 'success'){
                        $('select#produto-variacao').closest('div').html(returned.html);
                        getEstoqueVariacao($('select#produto-variacao'));
                    } else {
                        alert(\"Erro na pesquisa, tente novamente ou verifique se o produto selecionado não teve modificação.\");
                    }
                },
                error: function(data) {
                    
                }
            });
        }
    }
    
    function getEstoqueVariacao(click) {
        var value = $(click).val();
        if(value > 0){
            $.ajax({
                url: window.root_path + '/admin/ajax/getEstoqueProdutoByVariacao',
                type: 'POST',
                data: 'produto_variacao_id='+value,
                success: function(data){
                    var returned = $.parseJSON(data);
                    if(returned.retorno == 'success'){
                        $('div#produto-variacao-quantidade').closest('div').html(returned.html);
                    } else {
                        alert(\"Erro na pesquisa, tente novamente ou verifique se o produto selecionado não teve modificação.\");
                    }
                }
             });
        }
    };
    $('body').on('change', 'select[name=\"data[PRODUTO_ID]\"]',function(e) {
        
        getListProductVariation($(this));
    });    
    
    getListProductVariation($('select[name=\"data[PRODUTO_ID]\"]'));
    

</script>"));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));


$form->render();
