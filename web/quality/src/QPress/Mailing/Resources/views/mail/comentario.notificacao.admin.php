<?php $this->start(); ?>
<?php /* @var $comentario ProdutoComentario */ ?>

<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>

                <h2 style="<?php echo $this->style('h2') ?>">
                    Nova avaliação de produto recebida
                </h2>

                <p style="<?php echo $this->style('p') ?>">
                    Nova avaliação foi recebida para o produto <strong><?php echo $comentario->getProduto()->getNome() ?></strong>.
                </p>

                <p style="<?php echo $this->style('p') ?>">
                    <strong>Avaliação:</strong> <?php echo $comentario->getTitulo() . ' ('. $comentario->getNota() . ' estrelas)' ?>
                    <br>
                    <strong>Comentário:</strong><br>
                    "<i><?php echo nl2br($comentario->getDescricao()) ?></i>"
                    <br>
                    &minus; <i>por <?php echo $comentario->getNome() ?> (<?php echo $comentario->getEmail() ?>).</i>
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
