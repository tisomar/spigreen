<?php $this->start(); ?>

<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>
                <h2 style="<?php echo $this->style('h2') ?>">
                    Contato
                </h2>
                <p style="<?php echo $this->style('p') ?>">
                    Novo contato recebido via formulário do Q.Commerce. Seguem os dados:
                </p>
                <p style="<?php echo $this->style('p') ?>">
                    <b>Nome:</b> <?php echo $contato['nome'] ?>
                </p>
                <p style="<?php echo $this->style('p') ?>">
                    <b>E-mail:</b> <?php echo $contato['email'] ?>
                </p>
                <p style="<?php echo $this->style('p') ?>">
                    <b>Telefone:</b> <?php echo $contato['telefone'] ?>
                </p>
                <p style="<?php echo $this->style('p') ?>">
                    <b>Mensagem: </b><?php echo nl2br($contato['mensagem']) ?>
                </p>
            </td>
        </tr>
    </tbody>
</table>

<?php
$this->end('content');
$this->extend('mail/_layout');
