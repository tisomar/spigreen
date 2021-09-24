<?php
include_once __DIR__ . '/../includes/head.php';

function onlyNumbers($caracters)
{
    return preg_replace('/[^0-9]/', '', $caracters);
}

use \PFBC\Element\Hidden as Hidden;

$carrinho = $container->getCarrinhoProvider()->getCarrinho();

$form = new \PFBC\Form("bcash");

$form->configure(array(
    'action' => 'https://www.bcash.com.br/checkout/pay/',
    'method' => 'POST',
    'name'   => 'bcash',
));

// Dados do vendedor
$form->addElement(new Hidden("email_loja", Config::get('meio_pagamento.bcash.email')));
$form->addElement(new Hidden("tipo_integracao", 'PAD'));

// Dados do Comprador
$form->addElement(new Hidden("email", $carrinho->getCliente()->getEmail()));
$form->addElement(new Hidden("nome", $carrinho->getCliente()->getNomeCompleto()));
$form->addElement(new Hidden("cpf", onlyNumbers($carrinho->getCliente()->getCpf())));
$form->addElement(new Hidden("telefone", onlyNumbers($carrinho->getCliente()->getTelefone())));
$form->addElement(new Hidden("cliente_cnpj", onlyNumbers($carrinho->getCliente()->getCnpj())));
$form->addElement(new Hidden("cliente_razao_social", $carrinho->getCliente()->getRazaoSocial()));

// Dados da Entrega
$form->addElement(new Hidden("cep", onlyNumbers($carrinho->getEndereco()->getCep())));
$form->addElement(new Hidden("endereco", $carrinho->getEndereco()->getLogradouro()));
$form->addElement(new Hidden("cidade", $carrinho->getEndereco()->getCidade()->getNome()));
$form->addElement(new Hidden("estado", $carrinho->getEndereco()->getCidade()->getEstado()->getNome()));
$form->addElement(new Hidden("numero", $carrinho->getEndereco()->getNumero()));
$form->addElement(new Hidden("complemento", $carrinho->getEndereco()->getComplemento()));

$counter = 1;
foreach ($carrinho->getPedidoItemsJoinProdutoVariacao() as $objPedidoItem) {
    $form->addElement(new Hidden("produto_codigo_" . $counter, $objPedidoItem->getProdutoVariacao()->getProduto()->getId()));
    $form->addElement(new Hidden("produto_descricao_" . $counter, $objPedidoItem->getProdutoVariacao()->getProdutoNomeCompleto()));
    $form->addElement(new Hidden("produto_qtde_" . $counter, $objPedidoItem->getQuantidade()));
    $form->addElement(new Hidden("produto_valor_" . $counter, $objPedidoItem->getValorTotal()));
    $counter++;
}

$form->addElement(new Hidden("redirect_time", '10'));
$form->addElement(new Hidden("redirect", 'true'));
$form->addElement(new Hidden("frete", $carrinho->getValorEntrega()));
$form->addElement(new Hidden("id_pedido", $carrinho->getId()));
$form->addElement(new Hidden("url_retorno", get_url_site() . '/carrinho/confirmacao-bcash'));

$formHtml = $form->render(true);

$content = '
    ' . $formHtml . '

    <div class="container container-tertiary align-center">
        ' . get_loading_image() . '
        <br>
        <br>
        <h5>Você será redirecionado em instantes!<h5>
        <h5>Por favor, aguarde...<h5>
    </div>

    <script>
        window.onload=function(){ document.getElementById("bcash").submit(); };
    </script>
';
?>

<body itemscope itemtype="http://schema.org/WebPage" class="lightbox-page">
<?php echo get_contents(
    QCOMMERCE_DIR . '/documentos/_template.php',
    array(
        'title' => 'Pagamento com BCash',
        'content' => $content)
);
?>
<?php include_once __DIR__ . '/../includes/footer-lightbox.php' ?>
</body>
</html>
