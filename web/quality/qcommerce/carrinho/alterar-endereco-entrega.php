<?php
/**
 * Atualiza o endereço selecionado pelo cliente.
 */
$strIncludesKey = '';

$endereco = EnderecoQuery::create()
        ->filterByCliente(ClientePeer::getClienteLogado())
        ->filterById($container->getRequest()->query->get('endereco_id'))
        ->findOne();

if ($endereco instanceof Endereco) {
    $carrinho = $container->getCarrinhoProvider()->getCarrinho();
    $carrinho->setEndereco($endereco)->save();
    $carrinho->resetFrete();
}

include_once QCOMMERCE_DIR . '/includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage">

    <header class="container">
        <h1>Atualizando dados</h1>
    </header>
    <main role="main">
        <div class="container">
            <p>
                Por favor, aguarde! <br>
                Estamos atualizando as informações.
            </p>
        </div>
    </main>

    <?php include_once QCOMMERCE_DIR . '/includes/footer-lightbox.php' ?>
    <script>
        $(function() {
            window.parent.location.reload();
        });
    </script>
</body>
</html>
