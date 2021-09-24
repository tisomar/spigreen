<?php /* @var $cliente Cliente */ ?>

<?php $this->start(); ?>

    <table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
        <tbody>
        <tr>
            <td>
                <h2 style="<?php echo $this->style('h2') ?>">
                    Novo cliente cadastrado.
                </h2>

                <p style="<?php echo $this->style('p') ?>">
                    <?php echo $cliente->getNome() ?><br>
                    <?php echo $cliente->getCpf() ?><br>
                    <?php echo $cliente->getTelefone() ?><br>
                    <?php echo $cliente->getEmail() ?><br>
                    <?php if ($cliente->isPessoaJuridica()): ?>
                        <?php echo $cliente->getRazaoSocial() ?><br>
                        <?php echo $cliente->getNomeFantasia() ?><br>
                        <?php echo $cliente->getInscricaoEstadual() ?><br>
                        <?php echo $cliente->getCnpj() ?><br>
                    <?php endif; ?>
                </p>
            </td>
        </tr>
        </tbody>
    </table>

<?php
$this->end('content');
$this->extend('mail/_layout');
