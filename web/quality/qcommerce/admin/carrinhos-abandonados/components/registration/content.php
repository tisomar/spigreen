<?php
/* @var $object Pedido */

if (!$object->isAbandonado()) {
    $container->getSession()->getFlashBag()->set('error', 'O carrinho acessado não é considerado como abandonado!');
    redirectTo($config['routes']['list']);
    exit;
}
?>

<div class="alert">
    <h4>Enviar um e-mail ao cliente convidando-o a continuar seu carrinho de compras.</h4>
    <p>
        <a href="<?php echo get_url_admin() . '/carrinhos-abandonados/reactivate/?pedido_id=' . $object->getId() ?>" class="btn btn-green">
            <i class="icon-mail-reply-all"></i> Enviar
        </a>
        <?php echo !is_null($object->getDataAvisoAbandono()) ? 'Último envio em: ' . $object->getDataAvisoAbandono('d/m/Y H:i:s') : ''; ?>
    </p>
</div>

<div class="clearfix">
    <div class="pull-left">
        <h3>
            Pedido #<strong><?php echo $object->getId() ?></strong>
        </h3>
    </div>
</div>
<hr/>
<div class="row">
    <div class="col-md-3">
        <address>
            <strong>Cliente:</strong><br>
            <?php echo $object->getCliente()->getNomeCompleto() ?><br />
            <?php echo $object->getCliente()->getCodigoFederal() ?><br />
            <?php echo $object->getCliente()->getTelefone() ?><br />
            <?php echo $object->getCliente()->getEmail() ?><br /><br />
            <strong>Endereço:</strong><br>
            <?php echo $object->getEndereco() == null ? 'Não selecionado' : $object->getEndereco()->sprintf('CEP: %cep<br />%logradouro, %numero %complemento &minus; %bairro, %cidade &minus; %uf'); ?>
        </address>
    </div>

    <div class="col-md-3">
        <strong>Data do Carrinho:</strong><br><?php echo $object->getCreatedAt('d/m/Y H:i') ?>
        <br>
        <br>
        <strong>Data da Última Alteração:</strong><br><?php echo $object->getUpdatedAt('d/m/Y H:i') ?>
    </div>

</div>

<?php
/* @var $objPedidoStatus PedidoStatus */
/* @var $objPedidoStatusHistorico PedidoStatusHistorico */
?>

<br />

<legend>Itens do Pedido</legend>
<div class="table-responsive">
    <table class="table">
        <thead>
        <th>Produto</th>
        <th class="text-right">Quantidade</th>
        <th class="text-right">Valor Unitário</th>
        <th class="text-right">Valor Total</th>
        </thead>
        <tbody>
        <?php foreach ($object->getPedidoItems() as $objPedidoItem) : /* @var $objPedido PedidoItem */ ?>
            <tr>
                <td data-title="Produto"><?php echo $objPedidoItem->getProdutoVariacao()->getProdutoNomeCompleto() ?></td>
                <td data-title="Quantidade" class="text-right"><?php echo $objPedidoItem->getQuantidade() ?></td>
                <td data-title="Valor Un" class="text-right">R$ <?php echo format_money($objPedidoItem->getValorUnitario()) ?></td>
                <td data-title="Valor Total" class="text-right">R$ <?php echo format_money($objPedidoItem->getValorTotal()) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="pull-right">
    <p class="text-right">Subtotal: <strong>R$ <?php echo format_money($object->getValorItens()) ?></strong></p>

    <?php if ($object->getValorDescontoBy(CupomPeer::OM_CLASS) > 0) : ?>
        <p class="text-right">
            Cupom de desconto: &minus;<strong>R$ <?php echo format_money($object->getValorDescontoBy(CupomPeer::OM_CLASS)); ?></strong>
        </p>
    <?php endif; ?>

    <?php if ($object->getValorDescontoBy(PedidoFormaPagamentoPeer::OM_CLASS) > 0) : ?>
        <p class="text-right">
            Desconto no boleto: &minus;<strong>R$ <?php echo format_money($object->getValorDescontoBy(PedidoFormaPagamentoPeer::OM_CLASS)); ?></strong>
        </p>
    <?php endif; ?>

    <?php if ($object->getFrete()) : ?>
        <p class="text-right">
            Forma de Entrega (<?php echo $container->getFreteManager()->getModalidade($object->getFrete())->getTitulo() ?>):
            <strong>R$ <?php echo format_money($object->getValorEntrega()); ?></strong>
        </p>
    <?php endif; ?>
    <hr>
    <h3 class="text-right well-mini"><small>Total: </small> R$ <?php echo format_money($object->getValorTotal()) ?></h3>
</div>
