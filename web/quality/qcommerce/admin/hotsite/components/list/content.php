<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Cliente</th>
            <th>Nome</th>
            <th>Url</th>
            <th>Email</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($pager->getResult() as $object) { /* @var $object Hotsite */
            ?>
            <tr>
                <td data-title="Cliente que configurou o hotsite">
                    <?php echo $object->getCliente()->getNomeCompleto(); ?>
                </td>
                <td data-title="Nome">
                    <?php echo $object->getNome(); ?>
                </td>
                <td data-title="Url">
                    <a href="<?php echo  str_replace('https', 'http', get_url_site() . '/franqueado/') . $object->getSlug(); ?>" target="_blank">
                        <?php echo  str_replace('https', 'http', get_url_site() . '/franqueado/') . $object->getUrl(); ?>
                    </a>
                </td>
                <td data-title="Email">
                    <?php echo  $object->getEmail(); ?>
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
