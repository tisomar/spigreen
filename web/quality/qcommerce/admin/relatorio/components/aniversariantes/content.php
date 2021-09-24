<?php
use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Data de aniversário</th>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Telefone</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($pager->getResult() as $object) { /* @var $object Cliente */
            ?>
            <tr>
                <td data-title="Data de aniversário">
                    <?php echo $object->getDataNascimento('d/m'); ?>
                </td>
                <td data-title="Nome">
                    <?php echo $object->getNomeCompleto() ?>
                </td>
                <td data-title="E-mail">
                    <?php echo $object->getEmail() ?>
                </td>
                <td data-title="Telefone">
                    <?php echo $object->getTelefone() ?>
                </td>
            </tr>
        <?php } ?>
        <?php
        if (count($pager->getResult()) == 0) {
            ?>
            <tr>
                <td colspan="7">Nenhum registro disponível</td>
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
