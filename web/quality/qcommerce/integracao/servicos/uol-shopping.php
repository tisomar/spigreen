<?php
set_time_limit(0);

$produtos = UolShoppingItemQuery::create()
    ->joinProduto()
    ->find();

$schemeAndHttpHost = $container->getRequest()->getSchemeAndHttpHost();

$objXml = new DOMDocument("1.0", "ISO-8859-1");
$objXml->preserveWhiteSpace = false;
$objXml->formatOutput = true;

$root = $objXml->createElement("PRODUTOS");

/* @var $objProduto Produto */
foreach ($produtos as $item) {
    $objProduto = $item->getProduto();

    if ($objProduto->isDisponivel()) {
        $item = $objXml->createElement("PRODUTO");

        #<CODIGO>
        $codigo = $objXml->createElement("CODIGO", $objProduto->getSku());
        #<DESCRICAO>
        $descricao = $objXml->createElement("DESCRICAO", $objProduto->getDescricao());
        #<PRECO>
        $preco = $objXml->createElement("PRECO", format_number($objProduto->getValorBase(), UsuarioPeer::LINGUAGEM_INGLES));

        if ($objProduto->isPromocao()) {
            #<PRECO_PROMOCIONAL>
            $preco_promocional = $objXml->createElement("PRECO_PROMOCIONAL", format_number($objProduto->getValorPromocional(), UsuarioPeer::LINGUAGEM_INGLES));
        }

        #<NParcela>
        $nParcela = $objXml->createElement("NParcela", getParcelasByValor($objProduto->getValor()));
        #<Vparcela>
        $vParcela = $objXml->createElement("Vparcela", format_number($objProduto->getValor() / getParcelasByValor($objProduto->getValor()), UsuarioPeer::LINGUAGEM_INGLES));
        #<URL>
        $url = $objXml->createElement("URL", $schemeAndHttpHost . $objProduto->getUrlDetalhes());
        #<URL_IMAGEM>
        $url_imagem = $objXml->createElement("URL_IMAGEM", $schemeAndHttpHost . $objProduto->getImagemPrincipal()->getUrlImage());
        #<DEPARTAMENTO>
        $departamento = $objXml->createElement("DEPARTAMENTO", $objProduto->getCategoria() ? $objProduto->getCategoria() : '');

        #Criando Nós para <PRODUTO>
        $item->appendChild($codigo);
        $item->appendChild($descricao);
        $item->appendChild($preco);
        if ($objProduto->isPromocao()) {
            $item->appendChild($preco_promocional);
        }
        $item->appendChild($nParcela);
        $item->appendChild($vParcela);
        $item->appendChild($url);
        $item->appendChild($url_imagem);
        $item->appendChild($departamento);

        #adiciona o nó contato em (root) agenda
        $root->appendChild($item);
    }
}

#Fechando Nó root
$objXml->appendChild($root);

#cabeçalho da página
header("Content-Type: text/xml; charset=utf-8");

# imprime o xml na tela
print $objXml->saveXML();
