<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Categoria</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($pager->getResult() as $object) { /* @var $object CategoriaGoogleShopping */
                ?>
                <tr>
                    <td>
                        <?php echo escape($object->getNome()); ?>
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
