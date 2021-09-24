<?php
$collTransportadoraFaixaPeso = TransportadoraFaixaPesoQuery::create()
    ->filterByTransportadoraRegiaoId($request->query->get('importFrom'))
    ->find();

?>
<hr>
<div class="table-responsive">
    <?php if (count($collTransportadoraFaixaPeso) > 0) : ?>
        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>Peso</th>
                    <th>Valor</th>
                    <th>Prazo</th>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($collTransportadoraFaixaPeso as $object) { /* @var $object TransportadoraFaixaPeso */
                ?>
                <tr>
                    <td>até <b><?php echo $object->getPeso(); ?></b> gramas</td>
                    <td><b><?php echo $object->getValorFormatado(); ?></b>
                        <?php echo $object->getTipo() == TransportadoraFaixaPesoPeer::TIPO_PORCENTAGEM ? ' (do valor do pedido)' : ($object->getTipo() == TransportadoraFaixaPesoPeer::TIPO_PRECO_FIXO ? ' (valor fixo)' : ' (por kg)') ?>
                    </td>
                    <td>entrega em até <b><?php echo $object->getPrazoEntrega(); ?></b> dias</td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10">
                        <form method="post">
                            <input type="hidden" name="importFrom" value="<?php echo $request->query->get('importFrom') ?>">
                            <input type="hidden" name="importTo" value="<?php echo $request->query->get('importTo') ?>">
                            <button class="btn btn-primary pull-left"><i class="icon-download"></i> Importar</button>
                        </form>
                    </td>
                </tr>
            </tfoot>
        </table>
        <?php
    else :
        ?>
        Nenhuma faixa encontrada.
        <?php
    endif;
    ?>

</div>
