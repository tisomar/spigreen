<?php
use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th >Nome</th>
                <th >Breve descrição</th>
                <th ></th>
            </tr>
        </thead>
        <tbody>
            <?php
            /* @var $object Plano */
            foreach ($pager->getResult() as $object) {
                ?>
                <tr>
                    <td><?php echo resumo(escape($object->getNome()), 60, '...') ?></td>
                    <td><?php echo resumo(escape($object->getDescricao()), 100, '...');  ?></td>

                    <td class="text-right" data-title="Ações">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                Ações <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li><a title="Editar"  href="<?php echo $config['routes']['registration'] . '?id=' . $object->getId() ?>"><span class="icon-edit"></span> Editar</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            <?php
            if ($pager->count() == 0) {
                ?>
                <tr>
                    <td colspan="10">Nenhum registro encontrado</td>
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
