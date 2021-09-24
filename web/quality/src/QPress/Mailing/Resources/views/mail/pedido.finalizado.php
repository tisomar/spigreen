<?php $this->start(); ?>

<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>

                <h2 style="<?php echo $this->style('h2') ?>">
                    Finalização do Pedido #<?php echo $pedido->getId(); ?>
                </h2>

                <p style="<?php echo $this->style('p') ?>">
                    Informamos que seu pedido foi finalizado.
                </p>

                <h4 style="text-align: center; <?php echo $this->style('h4') ?>">
                    STATUS DO PEDIDO
                </h4>

                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td style="<?php echo $this->style('background-site') ?>">
                            <table align="" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="background: <?php echo $this->style('color-principal') ?>;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"> 
                    <tr>
                        <?php foreach ($allStatus as $pedidoStatus): ?>
                            <td style="<?php echo $this->style('p') ?> width: 25%; text-align: center">
                                <strong><?php
                                    echo str_replace(' ', '<br />', in_array($pedidoStatus->getId(), $pedidoStatusHistorico) ? $pedidoStatus->getLabelPosConfirmacao() : $pedidoStatus->getLabelPreConfirmacao())
                                    ?></strong>
                            </td>
                        <?php endforeach; ?>


                    </tr>
                </table>

                <br>
                <hr style="<?php echo $this->style('hr') ?>">
                <br>


                <h4 style="<?php echo $this->style('h4') ?>">
                    OUTRAS INFORMAÇÕES
                </h4>

                <p style="<?php echo $this->style('p') ?>">Em caso de dúvidas, entre em contato conosco através de nosso <a href="<?php echo get_url_site() ?>/contato/">formulário de contato</a>.</p>


            </td>
        </tr>
    </tbody>
</table>

<?php
$this->end('content');
$this->extend('mail/_layout');
