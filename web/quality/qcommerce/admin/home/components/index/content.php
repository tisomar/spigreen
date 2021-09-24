<div class="row">
    <div class="col-md-6">
        <ul class="nav nav-pills nav-justified">
            <li><a href="<?php echo get_url_admin() ?>/pedidos/list"><i class="icon-shopping-cart"></i> Últimos pedidos</a></li>
            <li><a href="<?php echo get_url_admin() ?>/clientes/list"><i class="icon-user"></i> Clientes</a></li>
        </ul>
        <hr>
        <ul class="nav nav-pills nav-justified">
            <li><a href="<?php echo get_url_admin() ?>/produtos/list"><i class="icon-folder-open"></i> Produtos</a></li>
            <li><a href="<?php echo get_url_admin() ?>/produtos/registration"><i class="icon-plus"></i> Novo produto</a></li>
        </ul>
        <br>
    </div>

    <div class="col-sm-3">
        <a class="info-tiles tiles-success" href="<?php echo get_url_admin() ?>/clientes/list">
            <div class="tiles-heading">Clientes</div>
            <div class="tiles-body-alt">
                <i class="icon-group"></i>
                <div class="text-center"><?php echo $countClientes ?></div>
                <small><?php echo plural($countClientes, 'novo cliente', 'novos clientes') ?></small>
            </div>
            <div class="tiles-footer">gerenciador de clientes</div>
        </a>
    </div>
    <div class="col-sm-3">
        <a class="info-tiles tiles-midnightblue" href="<?php echo get_url_admin() ?>/pedidos/list">
            <div class="tiles-heading">Pedidos</div>
            <div class="tiles-body-alt">
                <i class="icon-shopping-cart"></i>
                <div class="text-center"><?php echo $countPedidos ?></div>
                <small><?php echo plural($countPedidos, 'novo pedido', 'novos pedidos') ?></small>
            </div>
            <div class="tiles-footer">gerenciador de pedidos</div>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">

        <?php
        $collPedidos = PedidoQuery::create()
                ->filterByClassKey(1)
                ->filterByStatus(PedidoPeer::STATUS_ANDAMENTO)
                ->orderById(Criteria::DESC)
                ->limit(8)
                ->find();
        ?>
        <table class="table">
            <legend>Últimos pedidos</legend>
            <thead>
                <tr>
                    <th>ID</th>
                    <th class="text-center">Data</th>
                    <th>Cliente</th>
                    <th class="text-right">Valor da Compra</th>
                    <th class="text-center">Status Pagamento</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($collPedidos->count() > 0) {
                    foreach ($collPedidos as $object) { /* @var $object Pedido */
                        ?>
                        <tr>
                            <td><?php echo $object->getId() ?></td>
                            <td class="text-center"><?php echo $object->getCreatedAt('d/m/Y') ?></td>
                            <td><?php echo $object->getCliente()->getNomeCompleto() ?></td>
                            <td class="text-right">R$ <?php echo format_money($object->getValorTotal()) ?></td>
                            <td class="text-center"><h4><?php echo $object->getPedidoFormaPagamento()->getStatusLabel() ?></h4></td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                        <tr>
                            <td class="text-muted"><em>Nenhum pedido encontrado.</em></td>
                        </tr>
                        <?php
                }
                ?>
            </tbody>
        </table>
        <a href="<?php echo get_url_admin() ?>/pedidos/list" class="btn btn-primary btn-block"><i class="icon-shopping-cart"></i> Ver todos</a>
    </div>
</div>
