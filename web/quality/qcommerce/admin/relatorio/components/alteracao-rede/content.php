<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Nome Cliente Movido</th>
            <th>Nome Cliente Destino</th>
            <th>Usuário responsável</th>
            <th>Descrição</th>
            <th>Data</th>
        </tr>
        </thead>

        <tbody>
        <?php
        foreach ($pager as $object) : /* @var $object PlanoCarreiraHistorico */
            $clienteModivo  =  $clienteMover = ClienteQuery::create()->filterById($object->getClienteMovido())->findOne();
            $clienteDestino =  $clienteMover = ClienteQuery::create()->filterById($object->getClienteDestino())->findOne();
            ?>
            <tr>
                <td data-title="Nome Cliente Movido">
                    <?= $clienteModivo->getNomeCompleto() ?>
                </td>
                <td data-title="Nome Cliente Destino">
                    <?= $clienteDestino->getNomeCompleto() ?>
                </td>
                <td data-title="Nome Usuario">
                    <?= $object->getUpdater() ?>
                </td>
                <td data-title="Descricao">
                    <?= $object->getDescricao() ?>
                </td>
                <td data-title="Data">
                    <?= $object->getData('d/m/Y H:i') ?>
                </td>
            </tr>
            <?php
        endforeach;
        if ($pager->count() == 0) :
            ?>
            <tr>
                <td colspan="5">Nenhum registro disponível</td>
            </tr>
            <?php
        endif;
        ?>
        </tbody>
    </table>
</div>

<div class="col-xs-12 pull-right">
    <?php echo $pager->showPaginacao(); ?>
</div>