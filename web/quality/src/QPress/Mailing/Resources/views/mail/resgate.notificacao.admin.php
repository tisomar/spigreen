<?php $this->start(); ?>
<?php /* @var $comentario ProdutoComentario */ ?>

<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>

                <h2 style="<?php echo $this->style('h2') ?>">
                    Novo resgate concedido com sucesso.
                </h2>

                <p style="<?php echo $this->style('p') ?>">
                    Dados do Cliente que efetuou o Resgate:
                    <br />
                    <strong>Cliente:</strong> <?php echo $resgate->getCliente()->getNomeCompleto(); ?>
                    <strong>CPF/CNPJ:</strong> <?php echo $resgate->getCliente()->getCodigoFederal(); ?>
                    <br />
                    Dados do Resgate:
                    <br />
                    <strong>Data:</strong> <?php echo $resgate->getData(); ?>
                    <strong>Banco:</strong> <?php echo $resgate->getBanco(); ?>
                    <strong>Agência:</strong> <?php echo $resgate->getAgencia(); ?>
                    <strong>Conta Corrente:</strong> <?php echo $resgate->getConta(); ?>
                    <strong>Tipo da Conta:</strong> <?php echo $resgate->getTipoConta(); ?>
                    <strong>Valor:</strong> <?php echo $resgate->getValor(); ?>
                    <strong>Nome do Correntista:</strong> <?php echo $resgate->getNomeCorrentista(); ?>
                    <strong>CPF do Correntista:</strong> <?php echo $resgate->getCpfCorrentista(); ?>
                </p>

                <p style="<?php echo $this->style('p') ?>">
                    Você pode aprovar este comentário acessando o painel de administração do site.
                </p>

            </td>
        </tr>
    </tbody>
</table>

<?php
$this->end('content');
$this->extend('mail/_layout');
