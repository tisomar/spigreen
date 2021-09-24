<?php

use PFBC\Element;
?>

    <div class="col-xs-12">
        <p>
            <strong>Tipo: </strong><?php echo $object->getTipoDesc() ?>
        </p>
        <p>
            <strong>Destinatarios: </strong><?php echo $object->getDestinatariosDesc() ?>
        </p>
        <p>
            <strong>Título: </strong><?php echo $object->getTitulo() ?>
        </p>
        <p>
            <strong>Corpo: </strong>
            <br />
            <?php echo $object->getCorpo() ?>
        </p>
        <p>
            <strong>Somente Leitura: </strong><?php echo $object->getSomenteLeituraDesc() ?>
        </p>
    </div>
<?php if ($object->getTipoDest() == 'cliente') : ?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Data Leitura</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $arrIds = array_slice(explode(',', $object->getIdClientesStr()), 1, count(explode(',', $object->getIdClientesStr())) - 2);
            ?>
            <?php foreach ($arrIds as $idCliente) : ?>
                <?php
                $cliente = ClientePeer::retrieveByPK($idCliente);
                if ($cliente) :
                    $documentoAlertaCliente = DocumentoAlertaClientesQuery::create()
                        ->filterByCliente($cliente)
                        ->filterByDocumentoAlerta($object)
                        ->findOne();

                    ?>
                    <tr>
                        <td>
                            <?php echo $cliente->getNomeCompleto() ?>
                        </td>
                        <?php if ($documentoAlertaCliente != null) : ?>
                            <td>
                                <?php echo $object->getSomenteLeitura() == false ? $documentoAlertaCliente->getDataLido('d/m/Y H:m:s') : $documentoAlertaCliente->getDataCriacao('d/m/Y H:m:s') ?>
                            </td>
                        <?php else : ?>
                            <td></td>
                        <?php endif; ?>
                    </tr>
                    <?php
                endif;
            endforeach; ?>
        </tbody>

    </table>
</div>
<?php endif; ?>



