<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Data</th>
                <th>Produto</th>
                <th>Variação</th>
                <th>Operação</th>
                <th>Pedido</th>
                <th>Quantidade</th>
                <th>Centro</th>
                <?php if($grupoAdmin == 1) : ?>
                    <th>Observação</th>
                <?php endif?>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($pager->getResult() as $object) { /* @var $object EstoqueProduto */
                $centroDistribuicao = $object->getCentroDistribuicaoId() != null 
                ? CentroDistribuicaoPeer::retrieveByPK($object->getCentroDistribuicaoId())->getDescricao()
                : ''; 
                ?>
                <tr>
                    <td data-title="Data"><?= $object->getData('d/m/Y H:i:s'); ?></td>
                    <td data-title="Produto"><?= $object->getProdutoVariacao() ? $object->getProdutoVariacao()->getProduto()->getNome() : '---'; ?></td>
                    <td data-title="Variação"><?= $object->getProdutoVariacao() ? $object->getProdutoVariacao()->getSku() : '---'; ?></td>
                    <td data-title="Operação"><?= $object->getOperacaoDesc(); ?></td>
                    <td data-title="Pedido"><?= $object->getPedidoId(); ?></td>
                    <td data-title="Quantidade"><?= $object->getQuantidade(); ?></td>
                    <td data-title="CentroDistribuicao"><?= $centroDistribuicao; ?></td>
                    <?php if($grupoAdmin == 1) : ?>
                        <td data-title="Observação"><?= $object->getObservacao(); ?></td>
                    <?php endif?>
                </tr>
            <?php } ?>
            <?php
            if (count($pager->getResult()) == 0) {
                ?>
                <tr>
                    <td colspan="20">
                        Nenhum registro disponível
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>

    </table>
</div>

<div class="col-xs-12">
    <?php echo $pager->showPaginacao(); ?>
</div>
<script>
    function getListProductVariation(click){
        var value = $(click).val();
        var produtoThis = $(click);
        if(value > 0){
            $.ajax({
                url: window.root_path + "/admin/ajax/getProdutoVariacaoByFilterEstoque.php",
                type: "POST",
                data: "produto_id="+value,
                success: function(data){
                    var returned = $.parseJSON(data);
                    if(returned.retorno == "success"){
                        produtoThis.closest("div").next("div").html(returned.html);

                    } else {
                        alert("Erro na pesquisa, tente novamente ou verifique se o produto selecionado não teve modificação.");
                    }
                }
            });
        }
    }
    $("body").on("change", 'select[name="filter[ProdutoId]"]',function(e) {
        getListProductVariation($(this))
    });
    <?php $filters = $request->query->get('filter');
    if (isset($filters['ProdutoId']) && $filters['ProdutoId'] > 0) : ?>
        getListProductVariation($('select[name="filter[ProdutoId]"]'));
    <?php endif;
    ; ?>

</script>