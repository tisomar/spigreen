<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th>N° ticket</th>
                <th>Nome Completo - CPF</th>
                <th>Categoria</th>
                <th>Assunto</th>
                <th>Descrição</th>
                <th>Data de Cadastro</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($pager->getResult() as $object) { /* @var $object Cliente */
                ?>
                <tr>
                    <td data-title="Id"><?php echo '#' . escape($object->getId()); ?></td>
                    <td data-title="Nome/CPF">
                        <?php echo $object->getCliente()->getNomeCompleto(); ?>
                        <?php if($object->getCliente()->isPessoaJuridica()) :?>
                            <br /><?php echo $object->getCliente()->getCnpj(); ?>
                        <?php else : ?>
                            <br /><?php echo $object->getCliente()->getCpf(); ?>
                        <?php endif; ?>
                    </td>
                    <td data-title="Categoria"><?php echo escape($object->getCategoria()); ?></td>
                    <td data-title="Assunto"><?php echo escape($object->getAssunto()); ?></td>
                    <td data-title="Descricao"><?php echo escape($object->getDescricao()) ?></td>
                    <td data-title="Data Cadastro"><?php echo $object->getData('d/m/Y'); ?></td>
                    <td data-title="Status"><?php echo $object->getStatusDescricao(true); ?></td>
                    <td data-title="Ações" class="text-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                Ações <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li><a title="Editar"  href="<?php echo $config['routes']['registration'] . '?id=' . $object->getId() ?>"><span class="icon-edit"></span> Editar</a></li>

                                <li class="divider"></li>
                                <li><a title="Remover TIcket" class="remove-ticket" href="<?php echo ROOT_PATH . '/admin/ticket/list?ex=a&id=' . $object->getId() ?>" target="_self"><span class="icon-trash"></span> Excluir</a></li>
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
