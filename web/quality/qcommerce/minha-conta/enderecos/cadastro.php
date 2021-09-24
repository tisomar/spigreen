<?php
use QPress\Template\Widget;

$strIncludesKey = 'endereco-editar';

include_once QCOMMERCE_DIR . '/includes/security.php';
include_once __DIR__ . '/actions/cadastro.actions.php';


include_once QCOMMERCE_DIR . '/includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage" class="lightbox-page">

<?php
include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php';
include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php';

/**
 * Verifica se é uma atualização ou inclusão de endereço.
 *
 * 1. Caso o cliente esteja na página de finalização e alterou/incluiu um endereço, o sistema deve verificar se o endereço alterado
 *      é o mesmo que está no carrinho. Se for o mesmo, o sistema deve recalcular o frete com base nas novas informações.
 *      Do contrário, o sistema apenas faz reload da página.
 *
 * 2. Caso o cliente esteja em "Meus endereços" (no "minha conta"), o sistema apenas carrega a página novamente para mostrar a inclusão ou
 *      alteração do endereço.
 *
 */
if ($isNewOrUpdate) :
    ?>
    <script type="text/javascript">
        parent.window.location.reload();
    </script>
    <?php
endif;

$title = $objEndereco->isNew() ? 'Cadastrar novo endereço' : 'Editar endereço';
Widget::render('mfp-modal/header', array(
    'title' => $title
));

?>

<main role="main">
    <?php if ($isNewOrUpdate == false) : ?>
    <div class="box-flash-messages">
        <?php Widget::render('components/flash-messages'); ?>
    </div>
    <?php endif; ?>
    <div class="container">
        <form name="form-editar-endereco" id='form-cadastro-endereco' method="post" class="form-disabled-on-load">
            <?php include 'components/form.php'; ?>
            <div class="row">
                <div class="col-xs-12 col-sm-6 hidden-xs">
                    <div class="form-group">
                        <?php if ($container->getRequest()->query->get('isLightbox')) : ?>
                            <a href="javascript:parent.window.$.colorbox.close();" class="btn btn-default btn-block">Voltar</a>
                        <?php else : ?>
                            <a href="javascript:history.back()" class="btn btn-default btn-block">Voltar</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                        <button type="submit" class="btn btn-theme btn-block">
                            <?php if ($objEndereco->isNew()) : ?>
                                Salvar novo endereço
                            <?php else : ?>
                                Salvar
                            <?php endif; ?>
                        </button>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 visible-xs">
                    <div class="form-group">
                        <?php if ($container->getRequest()->query->get('isLightbox')) : ?>
                            <a href="javascript:parent.window.$.colorbox.close();" class="btn btn-default btn-block">Voltar</a>
                        <?php else : ?>
                            <a href="javascript:history.back()" class="btn btn-default btn-block">Voltar</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<?php
if (FlashMsg::hasErros()) {
    FlashMsg::display('danger');
}
include_once __DIR__ . '/../../includes/footer-lightbox.php';
?>

</body>
</html>
