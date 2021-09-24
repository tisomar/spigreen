<?php $this->start(); ?>
<?php $container = $GLOBALS['container']; ?>
<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>

                <h2 style="<?php echo $this->style('h2') ?>">
                    Status do Pedido #<?php echo $pedido->getId(); ?>
                </h2>

                <p style="<?php echo $this->style('p') ?>">
                    Seguem abaixo as informações do status de seu pedido:
                </p>

                <h4 style="text-align: center; <?php echo $this->style('h4') ?>">
                    STATUS DO PEDIDO
                </h4>

                <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td style="<?php echo $this->style('background-site') ?>">
                            <table align="" border="0" cellpadding="0" cellspacing="0" width="<?php echo $percent; ?>%">
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
                    <tr height="20">
                        <td colspan="5"></td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <p style="<?php echo $this->style('p') ?>">
                                <?php echo $pedido->getLastPedidoStatus()->getMensagem() ?>
                            </p>
                            <?php if ($pedido->getCodigoRastreio()): ?>
                                <p style="<?php echo $this->style('p') ?>">
                                    Você pode acompanhar o envio com o código de rastreamento 
                                    <a href="http://www.correios.com.br/sistemas/rastreamento/default.cfm"><?php echo $pedido->getCodigoRastreio(); ?></a>.
                                </p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>

                <br>
                <hr style="<?php echo $this->style('hr') ?>">
                <br>


                <h4 style="<?php echo $this->style('h4') ?>">
                    OUTRAS INFORMAÇÕES
                </h4>

                <?php if($pedido->getNumeroNotaFiscal() !== null) : ?>
                    <h2 style="<?php echo $this->style('h2') ?>">
                        N° nota fiscal <?php echo $pedido->getNumeroNotaFiscal(); ?>
                    </h2>
                <?php endif ?>           

                <?php if($pedido->getCodigoRastreio() !== null) : ?>
                    <h2 style="<?php echo $this->style('h2') ?>">
                        Código de rastreio <?php echo $pedido->getCodigoRastreio(); ?>
                    </h2>
                <?php endif ?>           

                <?php if($pedido->getLinkRastreio() !== null) : ?>
                    <h2 style="<?php echo $this->style('h2') ?>">
                        Link de rastreio <?php echo $pedido->getLinkRastreio(); ?>
                    </h2>
                <?php endif ?>           

                <?php if($pedido->getTransportadoraNome() !== null) : ?>
                    <h2 style="<?php echo $this->style('h2') ?>">
                        Transportadora <?php echo $pedido->getTransportadoraNome(); ?>
                    </h2>
                <?php endif ?>           

                <p style="<?php echo $this->style('p') ?>">Em caso de dúvidas, entre em contato conosco através de nosso <a href="<?php echo get_url_site() ?>/contato/">formulário de contato</a>.</p>


            </td>
        </tr>
    </tbody>
</table>

<?php
$this->end('content');
$this->extend('mail/_layout');
