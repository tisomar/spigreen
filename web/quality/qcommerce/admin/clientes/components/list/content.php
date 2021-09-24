<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Status</th>
                <th>Nome Completo - CPF</th>
                <th>Código Patrocinador</th>
                <th>Tipo</th>
                <th>Plano</th>
                <th>Contato</th>
                <th>Data de Cadastro</th>
                <th>Última Compra</th>
                <th>Número de Pedidos</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($pager->getResult() as $object) { /* @var $object Cliente */
                $pedido = PedidoQuery::create()
                    ->select(array('NrPedidos', 'UltimoPedido'))
                    ->withColumn('COUNT(1)', 'NrPedidos')
                    ->withColumn('(SELECT CREATED_AT FROM qp1_pedido WHERE CLASS_KEY = 1 and CLIENTE_ID = ' . $object->getId() . ' and STATUS = "FINALIZADO" ORDER BY CREATED_AT DESC LIMIT 1)', 'UltimoPedido')
                    ->filterByClassKey(1)
                    ->filterByClienteId($object->getId())
                    ->orderByCreatedAt(Criteria::DESC)
                    ->findOne();
                ?>
                <tr>
                    <td data-title="Status"><?php echo $object->getStatusDescricao(true); ?></td>
                    <td data-title="Nome/CPF">
                        <?php echo $object->getNomeCompleto(); ?>
                        <?php if($object->isPessoaJuridica()) :?>
                            <br /><?php echo $object->getCnpj(); ?>
                        <?php else : ?>
                            <br /><?php echo $object->getCpf(); ?>
                        <?php endif; ?>
                    </td>
                    <td data-title="Código Patrocinador"><?php echo escape($object->getChaveIndicacao()); ?></td>
                    <td data-title="Tipo"><?php echo ClientePeer::getTipoCliente($object->getId()) ?></td>
                    <td data-title="Plano"><?php echo escape($object->getPlano() ? $object->getPlano()->getNome() : ''); ?></td>
                    <td data-title="E-mail/Telefone"><?php echo $object->getEmail(); ?><br /><?php echo $object->getTelefone(); ?></td>
                    <td data-title="Data Cadastro"><?php echo $object->getCreatedAt('d/m/Y'); ?></td>
                    <td data-title="Última Compra"><?php echo $pedido['UltimoPedido'] ? date('d/m/Y', strtotime($pedido['UltimoPedido'])) : ''; ?></td>
                    <td data-title="Número de Pedidos"><?php echo $pedido['NrPedidos']; ?></td>
                    <td data-title="Ações" class="text-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                Ações <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <?php if ($podeAlterarCliente): ?>
                                    <li><a title="Editar"  href="<?php echo $config['routes']['registration'] . '?id=' . $object->getId() ?>"><span class="icon-edit"></span> Editar</a></li>
                                <?php endif; ?>

                                <li><a title="Central do Cliente" class="login-central" href="<?php echo ROOT_PATH . '/admin/clientes/login_central?id=' . $object->getId() ?>" target="_blank"><span class="icon-user"></span> Central do Cliente</a></li>

                                <?php if ($podeAlterarCliente): ?>
                                    <li class="divider"></li>
                                    <?php if (is_null($object->getTreeLeft())) :?>
                                        <li><a class="text-danger" title="Excluir" href="javascript:void(0);" data-href="<?php echo delete($_class, $object->getId()) ?>" data-action="delete" ><i class="icon-trash"></i> Excluir</a></li>
                                    <?php else : ?>
                                        <li><a title="Cliente Vago" class="cliente-vago" href="<?php echo ROOT_PATH . '/admin/clientes/list?ex=a&id=' . $object->getId() ?>" target="_self"><span class="icon-trash"></span> Excluir</a></li>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </td>
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

<script type="text/javascript">
    //$(document).ready(function() {
        $('.cliente-vago').on('click', function (e) {
            e.preventDefault();
            if (confirm("Deseja transformar esse cliente em vago?") == true) {
                location.href = $(this).attr('href');
            }
        })
    //});
</script>
