<?php $this->start(); ?>

<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>
                <p style="<?php echo $this->style('p') ?>; font-size: 15px;">
                    Olá <b><?php echo $pedido->getCliente()->getNome() ?></b>,
                </p>
                <p style="<?php echo $this->style('p') ?>">
                    Você tem produto(s) no carrinho e está a um passo de concluir o seu pedido.
                </p>
                <p style="<?php echo $this->style('p') ?>">
                    Clique abaixo para restaurar o seu carrinho de compras e conclua a sua compra.
                    <br>
                    <a href="<?php echo $pedido->getUrlReativation(); ?>">Desejo continuar comprando</a>
                </p>
            </td>
        </tr>
    </tbody>
</table>

<?php
$this->end('content');
$this->extend('mail/_layout');
