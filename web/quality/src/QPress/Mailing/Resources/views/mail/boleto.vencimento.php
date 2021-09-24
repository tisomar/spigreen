<?php $this->start(); ?>

<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>

                <h2 style="<?php echo $this->style('h2') ?>">
                    Aviso de vencimento de boleto
                </h2>

                <p style="<?php echo $this->style('p') ?>">
                    Olá <?php echo $cliente_nome ?>,
                </p>
                
                <p style="<?php echo $this->style('p') ?>">
                    O boleto bancário referente ao pagamento do pedido <strong><?php echo $pedido_id ?></strong> vence dia <strong><?php echo $data_vencimento ?></strong>.<br />
                    Você pode gerar a segunda via do boleto <a href="<?php echo $link2via ?>">aqui</a>
                </p>
                
                <p style="<?php echo $this->style('p') ?>">
                    Caso você já tenha efetuado o pagamento do mesmo, favor desconsiderar este e-mail.
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
