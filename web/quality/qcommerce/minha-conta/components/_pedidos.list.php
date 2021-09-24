<div class="container container-primary">
    <div class="text-group align-center">
        <h1 class="title-default title-detail">Lista de pedidos</h1>
        <ul class="list-secondary">
            <?php
            if (count($objPedidosAberto)) {
                foreach ($objPedidosAberto as $key => $pedido) {
                    echo get_contents(__DIR__ . '/pedido.detalhes.php', array('key' => $key, 'pedido' => $pedido, 'root_path' => $root_path, 'tipo' => 'Andamento'));
                }
            } else {
                ?>
                <div class="message-usage">
                    !<br />
                    Nenhum pedido em Aberto foi encontrado!<br />
                    <a href="<?php echo get_url_site() . '/produtos/' ?>">Clique aqui para começar suas compras.</a>
                </div>
                <?php
            }
            ?>
        </ul>
    </div>
</div>




<?php if (count($objPedidosFinalizados)) { ?>
    <h2>Finalizados e entregues</h2>    
    <ul id="pedidos-finalizados" class="lista_pedidos">
        <?php foreach ($objPedidosFinalizados as $key => $pedido) {
            echo get_contents(__DIR__ . '/pedido.detalhes.php', array('key' => $key, 'pedido' => $pedido, 'root_path' => $root_path, 'tipo' => 'Finalizado'));
        }  ?>

    </ul> <!-- /pedidos-finalizados -->
<?php } ?>
