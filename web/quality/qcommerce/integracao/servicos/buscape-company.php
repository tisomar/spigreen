<?php
set_time_limit(0);

$collProdutos = ProdutoQuery::create()
    ->joinBuscapeShoppingItem()
    ->filterByAtivo(1)
    ->useProdutoQuery()
    ->filterByDataExclusao(null)
    ->endUse()
    ->find();

$objXml = new DOMDocument("1.0", "ISO-8859-1");
$objXml->preserveWhiteSpace = false;
$objXml->formatOutput = true;

$root = $objXml->createElement("buscape");

$tag_data_atualizacao = $objXml->createElement("data_atualizacao", date('Y-m-d\TH:m:s\G\M\T-3'));
$root->appendChild($tag_data_atualizacao);

$tag_produtos = $objXml->createElement("produtos");

$schemeAndHttpHost = $container->getRequest()->getSchemeAndHttpHost();

# Formas de Pagamento:
# boleto | cartao_avista | cartao_parcelado_sem_juros | cartao_parcelado_com_juros

foreach ($collProdutos as $objProduto) { /* @var $objProduto Produto */
    if ($objProduto->isDisponivel()) {
        # produto
        $tag_produto = $objXml->createElement("produto");

        # produto > descricao
        $tag_descricao = $objXml->createElement("descricao", Integracao::caracteresEspeciaisXml($objProduto->getNome()));
        $tag_produto->appendChild($tag_descricao);

        # produto > canal_buscape
        $tag_canal_buscape = $objXml->createElement('canal_buscape');

        # produto > canal_buscape > canal_url
        $tag_canal_url = $objXml->createElement('canal_url', $schemeAndHttpHost . $objProduto->getUrlDetalhes());
        $tag_canal_buscape->appendChild($tag_canal_url);

        # produto > canal_buscape > valores
        $tag_valores = $objXml->createElement('valores');

        # produto > canal_buscape > valores > valor
        $tag_valor = $objXml->createElement('valor');

        # produto > canal_buscape > valores > valor > forma_de_pagamento
        $tag_forma_pagamento = $objXml->createElement('forma_de_pagamento', 'boleto');
        $tag_valor->appendChild($tag_forma_pagamento);

        # produto > canal_buscape > valores > valor > parcelamento
        $tag_parcelamento = $objXml->createElement('parcelamento', '1x de R$ ' . format_money(aplicarPercentualDesconto($objProduto->getValor(), Config::get('boleto.desconto_pagamento_avista'))));
        $tag_valor->appendChild($tag_parcelamento);

        # produto > canal_buscape > valores > valor > canal_preco
        $tag_canal_preco = $objXml->createElement('canal_preco', 'R$ ' . format_money(aplicarPercentualDesconto($objProduto->getValor(), Config::get('boleto.desconto_pagamento_avista'))));
        $tag_valor->appendChild($tag_canal_preco);

        # produto > canal_buscape > valores > valor
        $tag_valores->appendChild($tag_valor);

        # produto > canal_buscape > valores > valor
        $tag_valor = $objXml->createElement('valor');

        # produto > canal_buscape > valores > valor > forma_de_pagamento
        $tag_forma_pagamento = $objXml->createElement('forma_de_pagamento', 'cartao_avista');
        $tag_valor->appendChild($tag_forma_pagamento);

        # produto > canal_buscape > valores > valor > parcelamento
        $tag_parcelamento = $objXml->createElement('parcelamento', '1x de R$ ' . format_money($objProduto->getValor()));
        $tag_valor->appendChild($tag_parcelamento);

        # produto > canal_buscape > valores > valor > canal_preco
        $tag_canal_preco = $objXml->createElement('canal_preco', 'R$ ' . format_money($objProduto->getValor()));
        $tag_valor->appendChild($tag_canal_preco);

        # produto > canal_buscape > valores > valor
        $tag_valores->appendChild($tag_valor);

        # produto > canal_buscape > valores
        $tag_canal_buscape->appendChild($tag_valores);

        $tag_produto->appendChild($tag_canal_buscape);


        # produto > canal_lomadee
        $tag_canal_lomadee = $objXml->createElement('canal_lomadee');

        # produto > canal_lomadee > canal_url
        $tag_canal_url = $objXml->createElement('canal_url', $schemeAndHttpHost . $objProduto->getUrlDetalhes());
        $tag_canal_lomadee->appendChild($tag_canal_url);

        # produto > canal_lomadee > valores
        $tag_valores = $objXml->createElement('valores');

        # produto > canal_lomadee > valores > valor
        $tag_valor = $objXml->createElement('valor');

        # produto > canal_lomadee > valores > valor > forma_de_pagamento
        $tag_forma_pagamento = $objXml->createElement('forma_de_pagamento', 'boleto');
        $tag_valor->appendChild($tag_forma_pagamento);

        # produto > canal_lomadee > valores > valor > parcelamento
        $tag_parcelamento = $objXml->createElement('parcelamento', '1x de R$ ' . format_money(aplicarPercentualDesconto($objProduto->getValor(), Config::get('boleto.desconto_pagamento_avista'))));
        $tag_valor->appendChild($tag_parcelamento);

        # produto > canal_lomadee > valores > valor > canal_preco
        $tag_canal_preco = $objXml->createElement('canal_preco', 'R$ ' . format_money(aplicarPercentualDesconto($objProduto->getValor(), Config::get('boleto.desconto_pagamento_avista'))));
        $tag_valor->appendChild($tag_canal_preco);

        # produto > canal_lomadee > valores > valor
        $tag_valores->appendChild($tag_valor);

        # produto > canal_lomadee > valores > valor
        $tag_valor = $objXml->createElement('valor');

        # produto > canal_lomadee > valores > valor > forma_de_pagamento
        $tag_forma_pagamento = $objXml->createElement('forma_de_pagamento', 'cartao_avista');
        $tag_valor->appendChild($tag_forma_pagamento);

        # produto > canal_lomadee > valores > valor > parcelamento
        $tag_parcelamento = $objXml->createElement('parcelamento', '1x de R$ ' . format_money($objProduto->getValor()));
        $tag_valor->appendChild($tag_parcelamento);

        # produto > canal_lomadee > valores > valor > canal_preco
        $tag_canal_preco = $objXml->createElement('canal_preco', 'R$ ' . format_money($objProduto->getValor()));
        $tag_valor->appendChild($tag_canal_preco);

        # produto > canal_lomadee > valores > valor
        $tag_valores->appendChild($tag_valor);

        # produto > canal_lomadee > valores
        $tag_canal_lomadee->appendChild($tag_valores);

        $tag_produto->appendChild($tag_canal_lomadee);

        # produto > id_oferta
        $tag_id_oferta = $objXml->createElement("id_oferta", Integracao::caracteresEspeciaisXml($objProduto->getId()));
        $tag_produto->appendChild($tag_id_oferta);

        # produto > imagens
        $tag_imagens = $objXml->createElement("imagens");

            $tag_imagem = $objXml->createElement("imagem", $schemeAndHttpHost . $objProduto->getImagemPrincipal()->getUrlImage());
            $tag_imagem->setAttribute('tipo', 'O');

        $tag_imagens->appendChild($tag_imagem);

        $tag_produto->appendChild($tag_imagens);

        # produto > categoria
        $tag_categoria = $objXml->createElement("categoria", Integracao::caracteresEspeciaisXml($objProduto->getCategoria()->getNome()));
        $tag_produto->appendChild($tag_categoria);

        # produto > isbn
        $tag_isbn = $objXml->createElement("isbn", Integracao::caracteresEspeciaisXml(''));
        $tag_produto->appendChild($tag_isbn);

        # produto > cod_barra
        $tag_cod_barra = $objXml->createElement("cod_barra", Integracao::caracteresEspeciaisXml(''));
        $tag_produto->appendChild($tag_cod_barra);

        # produto > disponibilidade
        $tag_disponibilidade = $objXml->createElement("disponibilidade", $objProduto->getEstoqueAtual());
        $tag_produto->appendChild($tag_disponibilidade);

        # produto > marketplace
        $tag_marketplace = $objXml->createElement("marketplace", 'false');
        $tag_produto->appendChild($tag_marketplace);

        # produto > plataforma
        $tag_plataforma = $objXml->createElement("plataforma", 'qcommerce');
        $tag_produto->appendChild($tag_plataforma);

        $tag_produtos->appendChild($tag_produto);
    }
}//foreach

$root->appendChild($tag_produtos);

#Fechando N� root
$objXml->appendChild($root);

header("Content-Type: text/xml");

# imprime o xml na tela
print $objXml->saveXML();
