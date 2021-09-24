<?php include __DIR__ . '/../../config/menu.php'; ?>

<?php

/* @var $object Produto */
/* @var $container \QPress\Container\Container */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));
if ($object->getTipoProduto() == "COMPOSTO") {
    $form->addElement(new Element\HTML('
        <div class="row" style="margin-bottom: 10px;">
            <div class="col-lg-12">
    '));

    $form->addElement(new Element\HTML('
        <table class="table-responsive" style="width: 100%" id="products-table">
            <thead>
                <tr>
                    <td class="text-center">
                        Produto
                    </td>
                    <td class="text-center">
                        Variação
                    </td>
                    <td class="text-center">
                        Quantidade
                    </td>
                    <td class="text-center">
                        Valor Integração
                    </td>
                    <td>
                        &nbsp;
                    </td>
                </tr>
            </thead>
            <tbody>
            
    '));
    foreach ($arrProdutoCompostos as $objProdutoComposto) {
        /** @var $objProdutoComposto ProdutoComposto */

        $variacoesProduto = $objProdutoComposto->getProdutoRelatedByProdutoCompostoId()->getProdutoVariacaos();

        $form->addElement(new Element\HTML('
            <tr>
                <td class="produto-composto">
                    ' . get_form_select_object($arrProdutosSimples, $objProdutoComposto->getProdutoCompostoId(), 'getId', 'getNome', array('class' => 'form-control produto-composto','name' => 'data[produto-composto-id][]'), array('' => 'Selecione')) . '
                </td>
                <td>
                    ' . get_form_select_object($variacoesProduto, $objProdutoComposto->getProdutoCompostoVariacaoId(), 'getId', 'getSku', array('class' => 'form-control','name' => 'data[produto-composto-variacao-id][]'), array('' => 'Selecione')) . '
                </td>
                <td>
                    <input name="data[produto-composto-quantidade][]" class="form-control" type="number" value="' . $objProdutoComposto->getEstoqueQuantidade() . '" original-title="Quantidade Estoque">
                </td>
                <td>
                    <input name="data[produto-composto-valor-integracao][]" class="form-control text-right mask-money" value="R$ ' . number_format($objProdutoComposto->getValorIntegracao(), 2, ',', '.') . '" original-title="Valor Integracao">
                </td>
                
                <td class="text-center">
                    <button class="btn btn-danger" style="margin-top: -9px; margin-left: 10px;" onclick="RemoveTableRow(this)" type="button"><i class="icon-remove icon-white"></i></button>
                </td>
            </tr>
        '));
    }


    $form->addElement(new Element\HTML('
            <tr>
                <td>
                    ' . get_form_select_object($arrProdutosSimples, null, 'getId', 'getNome', array('class' => 'form-control','name' => 'data[produto-composto-id][]'), array('' => 'Selecione')) . '
                </td>
                <td>
                    <select class="form-control" name="data[produto-composto-variacao-id][]" disabled="disabled">
                        <option value="" selected="selected">Selecione</option>
                    </select>
                </td>

                <td>
                    <input name="data[produto-composto-quantidade][]" class="form-control" type="number" value="" original-title="Quantidade Estoque">
                </td>

                <td>
                    <input name="data[produto-composto-valor-integracao][]" class="form-control text-right mask-money" type="text" value="R$ 0,00" original-title="Valor Integracao">
                </td>

                <td class="text-center">
                    <button class="btn btn-danger" style="margin-top: -9px; margin-left: 10px;" onclick="RemoveTableRow(this)" type="button"><i class="icon-remove icon-white"></i></button>
                </td>
            </tr>
    '));

    $form->addElement(new Element\HTML('
            </tbody>
        </table>
    '));

    $form->addElement(new Element\HTML('
            </div>
        </div>
    '));

    $form->addElement(new Element\SaveButton('Salvar', 'submit', array('class' => 'text-center')));

    if ($object->isNew() == false) {
        $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
    }

    $form->addElement(new Element\Hidden('data[PRODUTO_ID]', $_GET['reference']));

    //$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));

    $form->addElement(new Element\HTML('
        <script>
            $( document ).ready(function(e) {
                RemoveTableRow = function(handler) {
                    var tr = $(handler).closest("tr");
        
                    tr.fadeOut(400, function(){
                        tr.remove();
                    });
        
                    return false;
                };
        
                AddTableRow = function() {
        
                    var newRow = $("<tr>");
                    var cols = "";
        
                    cols += \'<td class="produto-composto">\';
                    cols += "' . get_form_select_object($arrProdutosSimples, null, "getId", "getNome", array("class" => "form-control produto-composto","name" => "data[produto-composto-id][]"), array("" => "Selecione")) . '";
                    cols += "</td>";

                    cols += "<td>";
                    cols += \'<select class="form-control" name="data[produto-composto-variacao-id][]" disabled="disabled"><option value="" selected="selected">Selecione</option></select>\';
                    cols += "</td>";

                    cols += \'<td>\';
                    cols += \'<input name="data[produto-composto-quantidade][]" class="form-control" type="number" value="" original-title="Quantidade Estoque">\';
                    cols += "</td>";

                    cols += \'<td>\';
                    cols += \'<input name="data[produto-composto-valor-integracao][]" class="form-control text-right mask-money" value="R$ 0,00" original-title="Valor Integracao">\';
                    cols += "</td>";
        
                    cols += \'<td class="actions text-center">\';
                    cols += \'<button class="btn btn-danger" style="margin-top: -9px; margin-left: 10px;" onclick="RemoveTableRow(this)" type="button"><i class="icon-remove icon-white"></i></button>\';
                    cols += \'</td>\';
        
                    newRow.append(cols);
        
                    $("#products-table").append(newRow);

                    initMaskMoney();
                    return false;
        
                };  
                
            });
            
            $("body").on("change", \'select[name="data[produto-composto-id][]"]\',function(e) {
                var value = $(this).val();
                var produtoThis = $(this);
                if(value > 0){
                    $.ajax({
                        url: window.root_path + "/admin/ajax/getProdutoVariacao",
                        type: "POST",
                        data: "produto_id="+value,
                        success: function(data){
                            var returned = $.parseJSON(data);
                            
                            if(returned.retorno == "success"){
                                produtoThis.closest("td").next("td").html(returned.html);
                                
                            } else {
                                alert("Erro na pesquisa, tente novamente ou verifique se o produto selecionado não teve modificação.");
                            }
    
                        }
                    });
                }
            });
            
        </script>
    '));
} else {
    $form->addElement(new Element\HTML('
        <div class="panel">
            <div class="panel-body">
                Produto com variações não pode ser composto, modifique a opção do produto nas suas caracteristicas para adicionar produtos compostos.
                <br/>
                <a class="" href="' . get_url_admin() . '/produtos/registration?id=' . $_GET["reference"] . '">
                    Cliente aqui para voltar ao cadastro do produto</a>.
            </div>
        </div>
    '));
}

$form->render();
