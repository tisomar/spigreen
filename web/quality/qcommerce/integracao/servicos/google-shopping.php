<?php

set_time_limit(0);

$produtos = ItemGoogleShoppingQuery::create()
    ->filterByAtivo(1)
    ->useProdutoQuery()
    ->filterByDataExclusao(null)
    ->endUse()
    ->joinWith('ItemGoogleShopping.CategoriaGoogleShopping')
    ->find();

$schemeAndHttpHost = $container->getRequest()->getSchemeAndHttpHost();

$objXml = new DOMDocument("1.0", "UTF-8");
$objXml->preserveWhiteSpace = false;
$objXml->formatOutput = true;

$root = $objXml->createElement("rss");

$root->setAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
$root->setAttribute('version', '2.0');

$pai = $objXml->createElement("channel");

foreach ($produtos as $objItemGoogleShopping) { /* @var $objItemGoogleShopping ItemGoogleShopping */
    $urlImagem = $schemeAndHttpHost . $objItemGoogleShopping->getProduto()->getImagemPrincipal()->getUrlImage();
    $urlPagina = $schemeAndHttpHost . $objItemGoogleShopping->getProduto()->getUrlDetalhes();
    $nomeCategoriaGoogle = Integracao::caracteresEspeciaisXml($objItemGoogleShopping->getCategoriaGoogleShopping()->getNome());

    $nomeProduto = $objItemGoogleShopping->getProduto()->getNome();
    $nomeProduto = Integracao::caracteresEspeciaisXml($nomeProduto);

    $nomeMarca = Integracao::caracteresEspeciaisXml(
        $objItemGoogleShopping->getProduto()->getMarca() ?
        $objItemGoogleShopping->getProduto()->getMarca()->getNome() : 'N/I'
    );

    $descricaoProduto = $objItemGoogleShopping->getProduto()->getDescricao();
    $descricaoProduto = Integracao::caracteresEspeciaisXml($descricaoProduto);


    $item = $objXml->createElement("item");

    #<title>
    $title = $objXml->createElement("title", $nomeProduto);
    #<link>
    $link = $objXml->createElement("link", $urlPagina);
    #<description>
    $description = $objXml->createElement("description", $descricaoProduto);
    #<g:brand>
    $brand = $objXml->createElement("g:brand", $nomeMarca);

    $identifier = $objXml->createElement("g:identifier_exists", 'false');

    #<g:id>
    $id = $objXml->createElement("g:id", $objItemGoogleShopping->getProduto()->getId());
    #<g:condition>
    $condition = $objXml->createElement("g:condition", ItemGoogleShoppingPeer::translateProperty(ItemGoogleShoppingPeer::CONDICAO, $objItemGoogleShopping->getCondicao()));
    #<g:price>
    $price = $objXml->createElement("g:price", $objItemGoogleShopping->getProduto()->getValor() . " BRL");

    //Confere estoque
    if ($objItemGoogleShopping->getProduto()->isDisponivel()) {
        #<g:availability>
        $availability = $objXml->createElement("g:availability", "in stock");
    } else {
        #<g:availability>
        $availability = $objXml->createElement("g:availability", "out of stock");
    }

    #<g:image_link>
    $image_link = $objXml->createElement("g:image_link", $urlImagem);
    #<DEPARTAMENTO>
    $google_product_category = $objXml->createElement("g:google_product_category", $nomeCategoriaGoogle);

    #Criando Nós para <item>
    $item->appendChild($title);
    $item->appendChild($link);
    $item->appendChild($description);
    $item->appendChild($brand);
    $item->appendChild($identifier);
    $item->appendChild($id);
    $item->appendChild($condition);
    $item->appendChild($price);
    $item->appendChild($availability);

    $item->appendChild($image_link);
    $item->appendChild($google_product_category);

    /* Verifica se deve informar sobre o produto ser adulto ou nao */
    $adulto = ItemGoogleShoppingPeer::translateProperty(ItemGoogleShoppingPeer::ADULTO, $objItemGoogleShopping->getAdulto());
    if ($adulto != '') {
        $adult = $objXml->createElement("g:adult", $adulto);
        $item->appendChild($adult);
    }

    /* Verifica se deve informar sobre a faixa etaria do produto */
    $faixa = ItemGoogleShoppingPeer::translateProperty(ItemGoogleShoppingPeer::FAIXA_ETARIA, $objItemGoogleShopping->getFaixaEtaria());
    if ($faixa != '') {
        $ageGroup = $objXml->createElement("g:age_group", $faixa);
        $item->appendChild($ageGroup);
    }

    $genero = ItemGoogleShoppingPeer::translateProperty(ItemGoogleShoppingPeer::GENERO, $objItemGoogleShopping->getGenero());
    if ($genero != '') {
        $gender = $objXml->createElement("g:gender", $genero);
        $item->appendChild($gender);
    }

    /* Verifica se deve usar as imagens adicionais do produto */
    if ($objItemGoogleShopping->getUsarImagens()) {
        $collFotos = $objItemGoogleShopping->getProduto()->getFotos();
        if (count($collFotos) > 1) {
            $collFotos->shift();
            foreach ($collFotos as $objFoto) { /* @var $objFoto Foto */
                $imgLink = $schemeAndHttpHost . $objFoto->getUrlImage();
                $adtEle = $objXml->createElement("g:additional_image_link", Integracao::caracteresEspeciaisXml($imgLink));
                $item->appendChild($adtEle);
            }
        }
    }

    $produtoCategoria = CategoriaQuery::create()
        ->useProdutoCategoriaQuery()
            ->filterByProdutoId($objItemGoogleShopping->getProdutoId())
        ->endUse()
        ->findOne();
    if ($produtoCategoria instanceof ProdutoCategoria) {
        $nomeCategoria = $produtoCategoria->getFullName();
        $productType = $objXml->createElement("g:product_type", Integracao::caracteresEspeciaisXml($nomeCategoria));
        $item->appendChild($productType);
    }

    #adiciona o nó contato em (pai) channel
    $pai->appendChild($item);
}//foreach

#Fechando Nó Pai
$root->appendChild($pai);

#Fechando Nó root
$objXml->appendChild($root);

#cabeçalho da página
header("Content-Type: text/xml; charset=utf-8");

# imprime o xml na tela
print $objXml->saveXML();
