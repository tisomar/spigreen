<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($pager->getResult() as $object) { /* @var $object Conteudo */
                ?>
                <tr>
                    <td data-title="Conteúdo">
                        <?php echo $object->getNome(); ?><br />
                        <span class="text-muted"><?php echo $object->getTipoConteudo() ?></span>
                    </td>
                    <td data-title="Ações" class="text-right">
                        <a href="<?php echo get_url_admin() . '/' . $router->getModule() . '/registration?id=' . $object->getId() ?>" class="btn btn-default">
                            <i class="icon-edit"></i> Editar
                        </a>
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
