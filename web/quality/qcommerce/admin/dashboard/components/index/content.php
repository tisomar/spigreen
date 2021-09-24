<?php if ($menu->hasAccess('pedidos') || $menu->hasAccess('clientes') || $menu->hasAccess('produtos')) : ?>
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <?php if ($menu->hasAccess('pedidos')) : ?>
                    <div class="col-md-3">
                        <a class="info-tiles tiles-midnightblue" href="<?php echo get_url_admin() ?>/pedidos/list">
                            <div class="tiles-heading">Pedidos</div>
                            <div class="tiles-body-alt">
                                <i class="icon-shopping-cart"></i>
                                <div class="text-center"><?php echo $countPedidos ?></div>
                                <small><?php echo plural($countPedidos, 'novo pedido hoje', 'novos pedidos hoje') ?></small>
                            </div>
                            <div class="tiles-footer">gerenciador de pedidos</div>
                        </a>
                    </div>

                    <?php if($podeVisualizarEstatisticaVendas) : ?>
                    <div class="col-md-3">
                        <a class="info-tiles tiles-midnightblue" href="<?php echo get_url_admin() ?>/relatorio/volume-venda/?range=today">
                            <div class="tiles-heading">Vendas</div>
                            <div class="tiles-body-alt">
                                <i class="icon-money"></i>
                                <div class="text-center"><span class="text-top">R$</span> <?php echo format_money($totalizadores['valor_total_venda']); ?></div>
                                <small>Total de vendas hoje</small>
                            </div>
                            <div class="tiles-footer">relatório &raquo; volume de vendas</div>
                        </a>
                    </div>

                    <div class="col-md-3">
                        <a class="info-tiles tiles-midnightblue" href="<?= get_url_admin() ?>/relatorio/volume-faturamento/?range=today">
                            <div class="tiles-heading">Faturamento</div>
                            <div class="tiles-body-alt">
                                <i class="icon-money"></i>
                                <div class="text-center"><span class="text-top">
                                    R$</span> <?= format_money($totalizadores['valor_total_faturamento'] ?? 0); ?>
                                </div>
                                <small>Total faturado hoje</small>
                            </div>
                            <div class="tiles-footer">relatório &raquo; volume de faturamento</div>
                        </a>
                    </div>
                    <?php endif ?>

                <?php endif; ?>

                <?php if ($menu->hasAccess('clientes')) : ?>
                    <div class="col-md-3">
                        <a class="info-tiles tiles-success" href="<?php echo get_url_admin() ?>/clientes/list">
                            <div class="tiles-heading">Clientes</div>
                            <div class="tiles-body-alt">
                                <i class="icon-group"></i>
                                <div class="text-center"><?php echo $countClientes ?></div>
                                <small><?php echo plural($countClientes, 'novo cliente hoje', 'novos clientes hoje') ?></small>
                            </div>
                            <div class="tiles-footer">gerenciador de clientes</div>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
if ($menu->hasAccess('volume-venda')) :
    $showMenu = false;
    ?>
    <?php if($podeVisualizarEstatisticaVendas) : ?>
        <div class="panel panel-primary">
            <div class="panel-body">
                <h3 class="">
                    Pedidos com pagamento aprovado nos últimos 30 dias.
                    <br>
                    <small><a href="<?php echo get_url_admin() ?>/relatorio/volume-venda">Clique aqui</a> para acessar o relatório.</small>
                </h3>
                <hr>
                <div class="row">
                    <div class="row">
                        <?php 
                            include QCOMMERCE_DIR . '/admin/relatorio/actions/volume-venda/action.php'; 
                            include QCOMMERCE_DIR . '/admin/relatorio/components/volume-venda/content.php'; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif ?>
    <?php
endif; ?>

<div class="row">

<?php if ($menu->hasAccess('pedidos')) : ?>
        <div class="col-xs-12 col-md-3">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <div class="row">
                        <h3>Status por pedido</h3>
                        <table class="table">
                            <tbody>
                            <?php foreach ($countPedidoByStatus as $label => $count) : ?>
                                <?php $url = get_url_admin() . "/pedidos/list?filter%5BStatusHistorico%5D=" . $idPedidoByStatus[$label] . "&page=&is_filter=true" ?>
                                <tr>
                                    <td><a href="<?php echo $url; ?>"><?php echo $label ?></a></td>
                                    <td><b><?php echo $count ?></b></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <div class="row">
                        <table class="table">
                            <legend class="">
                                Últimos pedidos (24h)
                                <a href="<?php echo get_url_admin() ?>/pedidos/list" class="pull-right btn btn-link">Ver todos</a>
                            </legend>
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th class="text-center">Data</th>
                                <th>Cliente</th>
                                <th class="text-right">Valor da Compra</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if ($collPedidos->count() > 0) {
                                foreach ($collPedidos as $object) { /* @var $object Pedido */
                                    ?>
                                    <tr>
                                        <td><?php echo $object->getId() ?></td>
                                        <td class="text-center"><?php echo $object->getCreatedAt('d/m/Y H:i') ?></td>
                                        <td><?php echo $object->getCliente()->getNomeCompleto() ?></td>
                                        <td class="text-right">R$ <?php echo format_money($object->getValorTotal()) ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="4" class="text-muted"><em>Nenhum pedido novo encontrado.</em></td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

<?php endif; ?>

<?php if ($menu->hasAccess('clientes')) : ?>
        <div class="col-md-4">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <div class="row">
                        <table class="table">
                            <legend class="">
                                Novos clientes (24h)
                                <a href="<?php echo get_url_admin() ?>/clientes/list" class="pull-right btn btn-link">Ver todos</a>
                            </legend>
                            <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if ($collClientes->count() > 0) {
                                foreach ($collClientes as $object) { /* @var $object Cliente */
                                    ?>
                                    <tr>
                                        <td><?php echo $object->getNomeCompleto() ?></td>
                                        <td><?php echo $object->getStatusLabel() ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td class="text-muted"><em>Nenhum cliente novo encontrado.</em></td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

<?php endif; ?>

</div>




<?php if (UsuarioPeer::getUsuarioLogado()->isMaster()) : ?>
    <div class="row">

        <?php if (Config::get('has_google_shopping')) : ?>
            <div class="col-xs-12 col-sm-6">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h4>Copiar link do arquivo ".xml" para o Google Shopping</h4>
                    </div>
                    <div class="panel-footer">
                        <a href="javascript:prompt('Pressione Ctrl + C para copiar', '<?php echo $container->getRequest()->getSchemeAndHttpHost() . ROOT_PATH ?>/integracao/servicos/google-shopping')" class="btn btn-primary"><i class="icon-copy"></i> Copiar</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (Config::get('has_buscape')) : ?>
            <div class="col-xs-12 col-sm-6">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <h4>Copiar link do arquivo ".xml" para o Buscapé Company</h4>
                    </div>
                    <div class="panel-footer">
                        <a href="javascript:prompt('Pressione Ctrl + C para copiar', '<?php echo $container->getRequest()->getSchemeAndHttpHost() . ROOT_PATH ?>/integracao/servicos/buscape-company')" class="btn btn-primary"><i class="icon-copy"></i> Copiar</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

