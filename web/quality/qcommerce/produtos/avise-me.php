<?php
use QPress\Template\Widget;

/**
 * Actions ===================================================================================
 */

$strIncludesKey = 'avise-me';

$objProdutoVariacao = ProdutoVariacaoQuery::create()->findOneById($container->getRequest()->query->get('pvid'));

$clienteNome = $clienteEmail = $clienteTelefone = "";
if (ClientePeer::getClienteLogado()) {
    $clienteNome = ClientePeer::getClienteLogado()->getNomeCompleto();
    $clienteEmail = ClientePeer::getClienteLogado()->getEmail();
    $clienteTelefone = ClientePeer::getClienteLogado()->getTelefone();
}

if ($container->getRequest()->getMethod() == 'POST') {
    $clienteNome = $container->getRequest()->request->get('nome');
    $clienteEmail = $container->getRequest()->request->get('email');
    $clienteTelefone = $container->getRequest()->request->get('telefone');
    $produtoVariacaoId = $container->getRequest()->query->get('pvid');

    $response = ProdutoInteressePeer::cadastrar($clienteNome, $clienteEmail, $produtoVariacaoId, $clienteTelefone);

    if ($response->isSuccess) {
        FlashMsg::success('Você foi adicionado à lista de avisos para este produto.<br>Você receberá um aviso assim que o produto ficar disponível.');

        if (strpos($container->getRequest()->request->get('HTTP_REFERER'), '/carrinho') !== false) {
            $item = PedidoItemQuery::create()
                ->filterByPedidoId($container->getCarrinhoProvider()->getCarrinho()->getId())
                ->filterByProdutoVariacaoId($produtoVariacaoId)
                ->findOne();
            if ($item) {
                $container->getCarrinhoProvider()->getCarrinho()->removeItem($item->getId());
            }

            redirectTo(get_url_site() . '/carrinho/produto-avise-me/sucesso/carrinho?pvid=' . $objProdutoVariacao->getId());
            exit;
        } else {
            redirectTo(get_url_site() . '/produtos/avise-me/sucesso/?pvid=' . $objProdutoVariacao->getId());
            exit;
        }
    } else {
        if (is_array($response->errors) && count($response->errors) > 0) {
            FlashMsg::danger(implode($response->errors, "<br>&minus; "));
        } elseif (is_string($response->errors) && $response->errors != '') {
            FlashMsg::danger($response->errors);
        } else {
            throw new Exception('O retorno deve ser uma lista ou mensagem.');
        }
    }
}

if (Config::get('produto.proporcao') == '1:1') :
    $img = 'width=335&height=335&cropratio=1:1';
elseif (Config::get('produto.proporcao') == '4:3') :
    $img = 'width=335&height=250&cropratio=1.333:1';
elseif (Config::get('produto.proporcao') == '3:4') :
    $img = 'width=335&height=445&cropratio=0.75:1';
endif;

/**
 * LAYOUT ===================================================================================
 */

include_once __DIR__ . '/../includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage" class="lightbox">

<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.lead.tracking.php'; ?>

<?php
Widget::render('mfp-modal/header', array(
    'title' => 'Avise-me quando disponível'
));
?>

<main role="main">
    <div class="box-flash-messages">
        <?php Widget::render('components/flash-messages'); ?>
    </div>

    <?php if (isset($args[0]) && $args[0] == 'sucesso') : ?>
        <script>
            $(function() {
                if (window.parent.location.href.indexOf('/carrinho') != -1) {
                    window.parent.location.reload();
                } else {
                    new window.parent.PNotify({
                        text: 'Você foi adicionado à lista de avisos para este produto.<br>Você receberá um aviso assim que o produto ficar disponível.',
                        type: 'success',
                        delay: 6000,
                        icon: 'fa fa-check-circle'
                    });
                    window.parent.$.magnificPopup.close();
                }
            })
        </script>
    <?php endif; ?>
    <div class="container">

        <div class="row">
            <div class="col-xs-12 col-sm-4">
                <h4><?php echo $objProdutoVariacao->getProdutoNomeCompleto('</h4>'); ?>
                <img class="img-responsive lazy center-block" src="<?php echo $objProdutoVariacao->getProduto()->getUrlImageResize($img); ?>" alt="<?php echo $objProdutoVariacao->getProduto()->getNome(); ?>">
                <br>
                <p><?php echo resumo($objProdutoVariacao->getProduto()->getDescricao(), 250); ?></p>
            </div>
            <div class="col-xs-12 col-sm-8">
                <h4>
                    Deixe suas informações abaixo que lhe avisaremos através do e-mail quando este produto estiver disponível.
                </h4>
                <br>

                <form role="form" action="#" method="post" class="form-disabled-on-load jumbotron">
                    <input type="hidden" name="HTTP_REFERER" value="<?php echo $container->getRequest()->server->get('HTTP_REFERER') ?>" />

                    <div class="form-group">
                        <label for="nome">* Seu Nome:</label>
                        <input autofocus name="nome" class="form-control" type="text" id="nome" required value="<?php echo $clienteNome ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">* Seu E-mail:</label>
                        <input name="email" class="form-control" type="email" id="email" required value="<?php echo $clienteEmail ?>">
                    </div>
                    <div class="form-group">
                        <label for="telefone">* Seu Telefone:</label>
                        <input name="telefone" class="form-control mask-tel" type="text" id="telefone" required value="<?php echo $clienteTelefone ?>">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>
<?php include_once __DIR__ . '/../includes/footer-lightbox.php' ?>
</body>
</html>

