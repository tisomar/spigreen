<?php $this->start(); ?>

<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>

                <h2 style="<?php echo $this->style('h2') ?>">
                    Produto Disponível
                </h2>

                <p style="<?php echo $this->style('p') ?>">
                    Informamos que o produto 
                    <a href="<?php echo get_url_site() . get_url_caminho($produtoVariacao->getProduto()->getUrlDetalhes()) ?>">
                        <?php echo $produtoVariacao->getProdutoNomeCompleto(' ') ?>
                    </a>&nbsp;já está disponível em nossa loja virtual.<br><br>
                    Venha conferir!
                </p>
                
                <hr style="<?php echo $this->style('hr') ?>">
                
                <p style="<?php echo $this->style('p') ?>">
                    Caso não consiga abrir o link acima, copie e cole a url abaixo em seu navegador.<br>
                    <?php echo get_url_site() . get_url_caminho($produtoVariacao->getProduto()->getUrlDetalhes()) ?>
                </p>

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
