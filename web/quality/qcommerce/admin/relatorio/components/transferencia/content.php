<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Cliente Remetente / Cod Patrocinador</th>
                <th>Cliente Destinatário / Cod Patrocinador</th>
                <th>Data Transferência</th>
                <th>Valor Transferido</th>
            </tr>
        </thead>

        <tbody>
        <?php
        foreach ($pager as $object) : /* @var $object PlanoCarreiraHistorico */
            $clienteRemetente = ClientePeer::retrieveByPK($object->getClienteRemetenteId());
            $clienteDestinatario = ClientePeer::retrieveByPK($object->getClienteDestinatarioId());
            ?>
            <tr>
                <td data-title="cliente-remetente">
                    <?=
                    $clienteRemetente->getNomeCompleto()
                    . ' - ' .
                    $clienteRemetente->getChaveIndicacao();
                    ?> 
                </td>
                <td data-title="cliente-destinatario"> 
                    <?=
                    $clienteDestinatario->getNomeCompleto()
                    . ' - ' .
                    $clienteDestinatario->getChaveIndicacao();
                    ?>
                </td>
                <td data-title="data"><?= $object->getData('d/m/Y') ?></td>
                <td data-title="valor"><?= $object->getQuantidadePontos() ?> </td>
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