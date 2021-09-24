<?php

use QPress\Template\Widget;

header('Content-Type: application/json');

$produtoId = $container->getRequest()->request->get('produto_id');
$atributosUnparsed = $container->getRequest()->request->get('atributos', []);

parse_str($atributosUnparsed, $atributos);

$jsonResponse = [];

foreach ($atributos['opcoes'] as $produtoId => $opcoes) {
    $produto = ProdutoQuery::create()->findOneById($produtoId);

    $query = ProdutoVariacaoQuery::create()
        ->clearSelectColumns()
        ->select(['ID'])
        ->withColumn(ProdutoVariacaoPeer::ID, 'ID')
        ->filterByProdutoId($produtoId)
        ->filterByDataExclusao(null, Criteria::ISNULL);

    foreach ($opcoes as $_atributo_id => $_atributo_descricao) {
        $query
            ->useProdutoVariacaoAtributoQuery('t' . $_atributo_id)
                ->filterByProdutoAtributoId($_atributo_id)
                ->filterByDescricao($_atributo_descricao)
            ->endUse();
    }

    $produto_variacao_id = $query->find()->toArray();

    $c = ProdutoVariacaoAtributoQuery::create()
        ->_if(count($produto_variacao_id) > 0)
            ->filterByProdutoVariacaoId($produto_variacao_id)
        ->_endif()
        ->filterByProdutoAtributoId(array_keys($opcoes), Criteria::NOT_IN);

    $opcoesDisponiveis  = ProdutoVariacaoAtributoPeer::getOpcoesDisponiveis($c, $produtoId);
    $arrayOpcoes        = ProdutoVariacaoAtributoPeer::getOpcoesDisponiveisToArray($produtoId, $opcoesDisponiveis);

    // ----------------------------------

    $jsonResponse[$produtoId]['items']                 = $arrayOpcoes;
    $jsonResponse[$produtoId]['produto_variacao_id']   = null;
    $jsonResponse[$produtoId]['aviseme']               = null;

    // Atualiza a variação de acordo com as opções informadas
    if (isset($produto_variacao_id) && count($produto_variacao_id) == 1) {
        $produtoVariacao = ProdutoVariacaoQuery::create()->findOneById($produto_variacao_id[0]);
        if ($produtoVariacao instanceof ProdutoVariacao) {
            if ($produtoVariacao->isDisponivel() && $produtoVariacao->getEstoqueAtual() > 0) {
                $jsonResponse[$produtoId]['produto_variacao_id'] = $produtoVariacao->getId();
            } else {
                $jsonResponse[$produtoId]['aviseme'] = $produtoVariacao->getId();
            }
        }
    } else {
        $produtoVariacao = $produto->getProdutoVariacao();
    }

    // Atualização das informações relacionadas à variação.
    if (count($jsonResponse[$produtoId]['items']) == 0 && is_null($jsonResponse[$produtoId]['produto_variacao_id'])) {
        // Nome e referência
        $jsonResponse[$produtoId]['data_content_id']['produto_detalhe_box_title_' . $produtoId] =
            Widget::render('produto-detalhe/box_title', array(
                'objProdutoVariacao' => $produtoVariacao
            ), true);

        // Valor do produto
        $jsonResponse[$produtoId]['data_content_id']['produto_price_' . $produtoId] =
            Widget::render('produto-detalhe/indisponivel', array(), true);

        // Botão de comprar
        $jsonResponse[$produtoId]['data_content_id']['produto_detalhe_btn_comprar_' . $produtoId] =
            Widget::render('produto-detalhe/btn_aviseme', array(
                'variacaoId' => $produtoVariacao->getId()
            ), true);

        // Quantidade
        $jsonResponse[$produtoId]['data_content_id']['produto_detalhe_box_quantity_' . $produtoId] =
            '';

        // Frete
        $jsonResponse[$produtoId]['data_content_id']['update-box-frete'] =
            '<script id="script-box-frete">$(function() { $("#box-frete").show(); $("#script-box-frete").remove(); })</script>';

        $jsonResponse[$produtoId]['data_content_id']['disponibilidade_' . $produtoId] =
            Widget::render('components/alert', array(
                'type' => 'danger',
                'message' => sprintf('Variação %s indisponível no momento', implode('/', $opcoes)),
            ), true);
    } else {
        // Nome e referência
        $jsonResponse[$produtoId]['data_content_id']['produto_detalhe_box_title_' . $produtoId] =
            Widget::render('produto-detalhe/box_title', array(
                'objProdutoVariacao' => $produtoVariacao
            ), true);

        // Valor do produto
        $jsonResponse[$produtoId]['data_content_id']['produto_price_' . $produtoId] =
            Widget::render('produto/price', array(
                'objProdutoVariacao' => $produtoVariacao
            ), true);

        // Botão comprar
        $jsonResponse[$produtoId]['data_content_id']['produto_detalhe_btn_comprar_' . $produtoId] =
            Widget::render("produto-detalhe/btn_comprar", array(
                'objProduto' => $produto
            ), true);

        // Quantidade
        $jsonResponse[$produtoId]['data_content_id']['produto_detalhe_box_quantity_' . $produtoId] =
            Widget::render("produto-detalhe/box_quantity", array(
                'objProdutoVariacao' => $produtoVariacao
            ), true);

        // Frete
        $jsonResponse[$produtoId]['data_content_id']['update-box-frete'] =
            '<script id="script-box-frete">$(function() { $("#box-frete").show(); $("#script-box-frete").remove(); })</script>';

        $jsonResponse[$produtoId]['data_content_id']['disponibilidade_' . $produtoId] = "";
    }

    $fotos = FotoQuery::create()->filterByCor($opcoes, Criteria::IN)->filterByProdutoId($produtoId)->find();
    if ($fotos->count() == 0) {
        $fotos = $produto->getFotosByCor(null);
    }

    $jsonResponse[$produtoId]['data_content_id']['owl_fotos_item_' . $produtoId] = "";
    $jsonResponse[$produtoId]['data_content_id']['owl_miniaturas_item_' . $produtoId] = "";
    $jsonResponse[$produtoId]['data_content_id']['swiper_item_' . $produtoId] = "";

    foreach ($fotos as $foto) {
        $jsonResponse[$produtoId]['data_content_id']['owl_fotos_item_' . $produtoId] .=
            Widget::render('gallery/owl-fotos-item', array(
                'foto' => $foto,
            ), true);

        $jsonResponse[$produtoId]['data_content_id']['owl_miniaturas_item_' . $produtoId] .=
            Widget::render('gallery/owl-miniaturas-item', array(
                'foto' => $foto,
            ), true);

        $jsonResponse[$produtoId]['data_content_id']['swiper_item_' . $produtoId] .=
            Widget::render('gallery/swiper-item', array(
                'foto' => $foto,
            ), true);
    }
}

echo json_encode($jsonResponse);
