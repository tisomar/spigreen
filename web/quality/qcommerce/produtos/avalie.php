<?php
/* @var $objComentario ProdutoComentario */
/* @var $objCliente Cliente */

use QPress\Template\Widget;
$strIncludesKey = 'produto-avalie';

include_once __DIR__ . '/actions/avalie.actions.php';
include_once __DIR__ . '/../includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage" class="lightbox" data-page="<?php echo $strIncludesKey ?>">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<script type="text/javascript">
    window.starRate = jQuery.parseJSON( '<?php echo json_encode(ProdutoComentarioPeer::getNotasDescricao()) ?>' );
</script>

<?php if ($isSuccess) : ?>
    <script>
        $(function() {
            new window.parent.PNotify({
                text: 'Sua avaliação foi enviada com sucesso. Obrigado!',
                type: 'success',
                delay: 6000,
                icon: 'fa fa-check-circle'
            });
            window.parent.$.magnificPopup.close();
        });
    </script>
<?php endif; ?>

<?php
Widget::render('mfp-modal/header', array(
    'title' => 'Avaliação do produto'
));
?>

<main role="main">
    <div class="box-flash-messages">
        <?php Widget::render('components/flash-messages'); ?>
    </div>
    <div class="container">
        <?php if (($objClienteAvaliacao || ClientePeer::isAuthenticad())) : ?>
<!--            --><?php //if ($isSuccess) { ?>
<!---->
<!--                <div class="text-center">-->
<!--                    <p>-->
<!--                        A sua participação é muito importante para nós. Seus comentários são sempre bem-vindos,-->
<!--                        e neles, você pode expressar sua opinião sobre um item adquirido utilizando o nosso site.-->
<!--                    </p>-->
<!--                    <br>-->
<!--                    <a class="btn btn-theme" href="javascript:void(0)" onclick="parent.$.colorbox.close()">FECHAR JANELA</a>-->
<!--                </div>-->
<!---->
<!--            --><?php //} else { ?>

                <form role="form" method="post" name="form-comentario" class="form-disabled-on-load">
                    <?php
                    Widget::render('components/rating', array(
                        'id'        =>  'rating',
                        'name'      =>  'avaliacao[NOTA]',
                        'required'  =>  true
                    ));
                    ?>
                    <p>Selecione uma estrela e deixe seu comentário sobre o produto.</p>
                    <hr>

                    <div class="form-group">
                        <label for="avaliation-title">* Título:</label>
                        <input type="text" class="form-control" name="avaliacao[TITULO]" id="rating-title" maxlength="50" required value="<?php echo $objComentario->getTitulo() ?>">
                    </div>
                    <div class="form-group">
                        <label for="avaliation-comment">* Deixe seu comentário:</label>
                        <textarea class="form-control" name="avaliacao[DESCRICAO]" id="avaliation-comment" required><?php echo $objComentario->getDescricao() ?></textarea>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-theme btn-block" type="submit">Enviar Avaliação</button>
                    </div>
                </form>
<!--            --><?php //} ?>
        <?php else : ?>
            <p> É necessário estar logado para poder comentar e avaliar este produto.<br>
                Por favor, <a class="btn-redirect-login" href="javascript:void(0)">clique aqui</a> para ir para a tela de login.
            </p>
        <?php endif; ?>
    </div>
</main>

<?php if (FlashMsg::hasSucessos()) : ?>
    <script type="text/javascript">
        parent.window.location = '<?php echo $objComentario->getProduto()->getUrlDetalhes() ?>';
    </script>
<?php endif; ?>

<script type="text/javascript">

    $(function() {
        $('#rating').on('rating.change', function (event, value, caption) {
            $('#rating-title').attr('value', window.starRate[value]);
        });
        $('body').on('click', '.btn-redirect-login', function() {
            parent.window.location = '<?php echo get_url_site() ?>/login?redirecionar=' + parent.window.location.href + '#box-avalie';
        });
    });
</script>

<?php include_once QCOMMERCE_DIR . '/includes/footer-lightbox.php' ?>

</body>
</html> 
