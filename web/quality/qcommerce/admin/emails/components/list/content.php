<?php use PFBC\Element; ?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Status</th>
                <th>Remetente</th>
                <th>Destinatário</th>
                <th>Assunto</th>
                <th>Conteúdo</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($pager->getResult() as $object) { /* @var $object EmailLog */
                ?>
                <tr>
                    <td data-title="Status">
                        <?php
                        echo $object->getStatusLabel();
                        echo $object->getStatus() == EmailLogPeer::STATUS_ENVIADO
                            ? '<div class="text-muted"><small>' . $object->getDataEnvio('d/m/Y H:i:s') . '</small></div>'
                            : ''
                        ?>
                    </td>
                    <td data-title="Remetente"><?php echo $object->getRemetente(); ?></td>
                    <td data-title="Destinatário"><?php echo $object->getDestinatario(); ?></td>
                    <td data-title="Assunto"><?php echo $object->getAssunto(); ?></td>
                    <td data-title="Conteúdo"><a href="<?php echo get_url_admin() . '/emails/view/' . md5($object->getId())  ?>" target="_blank">ver email</a></td>
                    <td data-title="Ações" class="text-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                Ações <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li><a title="Reenviar"  href="#"><span class="icon-reply"></span> Reenviar</a></li>
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
