<?php $this->start(); ?>
<?php /* @var $comentario ProdutoComentario */ ?>

<table class="mainContent" align="center" border="0" cellpadding="0" cellspacing="0" width="528">
    <tbody>
        <tr>
            <td>

                <h2 style="<?php echo $this->style('h2') ?>">
                    Obrigado por enviar sua avaliação
                </h2>

                <p style="<?php echo $this->style('p') ?>">
                    A sua participação é muito importante para nós. Seus comentários são sempre bem-vindos,
                    e neles, você pode expressar sua opinião sobre um item adquirido utilizando o nosso site.
                </p>

            </td>
        </tr>
    </tbody>
</table>

<?php
$this->end('content');
$this->extend('mail/_layout');
