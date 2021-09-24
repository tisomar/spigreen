<?php $this->start(); ?>

<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>

                <h2 style="<?php echo $this->style('h2') ?>">
                    Produto com estoque mínimo atingido
                </h2>

                <p style="<?php echo $this->style('p') ?>">
                    O produto <b><?php echo $produtoVariacao->getProdutoNomeCompleto(' ') ?></b> atingiu o
                    seu estoque mínimo (<?php echo 'Atual: ' . $produtoVariacao->getEstoqueAtual() . ' | Mín: ' . $produtoVariacao->getEstoqueAtual() ?>) configurado no Q.CMS.
                </p>
                
                <br>
                <hr style="<?php echo $this->style('hr') ?>">
                <br>
            </td>
        </tr>
    </tbody>
</table>

<?php
$this->end('content');
$this->extend('mail/_layout');
