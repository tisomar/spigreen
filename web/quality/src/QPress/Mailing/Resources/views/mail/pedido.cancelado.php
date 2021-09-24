<?php $this->start(); ?>

<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>

                <h2 style="<?php echo $this->style('h2') ?>">
                    Finalização do Pedido #<?php echo $pedido->getId(); ?>
                </h2>

                <p style="<?php echo $this->style('p') ?>">
                    Informamos que seu pedido foi cancelado.
                </p>

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
