<?php

$arrEstoque = EstoqueProdutoQuery::create(null, $query)->orderById(Criteria::DESC)->find();

use PFBC\Element;
?>
<div class="col-xs-12 noprint">
    <a href="#" class="btn btn-default pull-right" onclick="javascript:window.print();">Imprimir</a>
</div>
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
                <th>Centro de Distribuicao</th>
                <th>Observação</th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($pager as $object) : /* @var $object Cliente */
            $centroDistribuicao = '';
            if($object->getCentroDistribuicaoId() !== null) :
                $centroDistribuicao = CentroDistribuicaoQuery::create()->findOneById($object->getCentroDistribuicaoId());
                $centroDistribuicao = $centroDistribuicao->getDescricao();
            endif;
            ?>
             <tr>
                <td data-title="Data"><?php echo $object->getData('d/m/Y H:i:s'); ?></td>
                <td data-title="Produto"><?php echo $object->getProdutoVariacao() ? $object->getProdutoVariacao()->getProduto()->getNome() : '---'; ?></td>
                <td data-title="Variação"><?php echo $object->getProdutoVariacao() ? $object->getProdutoVariacao()->getSku() : '---'; ?></td>
                <td data-title="Operação"><?php echo $object->getOperacaoDesc(); ?></td>
                <td data-title="Pedido"><?php echo $object->getPedidoId(); ?></td>
                <td data-title="Quantidade"><?php echo $object->getQuantidade(); ?></td>
                <td data-title="CentroDistribuicao"><?php echo $centroDistribuicao; ?></td>
                <td data-title="Observação"><?php echo $object->getObservacao(); ?></td>
            </tr>
            <?php
        endforeach
        ?>
        <?php if ($pager->count() == 0) : ?>
            <tr>
                <td colspan="5">Nenhum registro disponível</td>
            </tr>
        <?php endif ?>
        </tbody>
    </table>
</div>

<div class="col-xs-12">
    <?= $pager->showPaginacao(); ?>
</div>
