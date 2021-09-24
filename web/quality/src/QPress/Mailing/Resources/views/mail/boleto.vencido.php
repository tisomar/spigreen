<?php $this->start(); ?>

<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>

                <h2 style="<?php echo $this->style('h2') ?>">
                    Aviso de vencimento de boleto
                </h2>

                <p style="<?php echo $this->style('p') ?>">
                    Informamos que <?php echo plural(count($pedidos), 'o pedido '.  implode(', ', $pedidos).' venceu', 'os pedidos %s venceram'); ?> em <?php echo $data_vencimento ?> e não recebemos a confirmação de pagamento.
                </p>
                
            </td>
        </tr>
    </tbody>
</table>

<?php
$this->end('content');
$this->extend('mail/_layout');
