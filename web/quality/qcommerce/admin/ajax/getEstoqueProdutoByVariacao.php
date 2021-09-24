<?php

if (isset($_POST['produto_variacao_id']) && is_numeric($_POST['produto_variacao_id']) && $_POST['produto_variacao_id'] > 0) {
    $objProdutoVariacao = ProdutoVariacaoQuery::create()->findOneById($_POST['produto_variacao_id']);

    if ($objProdutoVariacao instanceof ProdutoVariacao) {
        $html = '';
        $html .= '<p class="col-sm-offset-3 col-sm-6">Estoque atual total somando todos os cds = ' . $objProdutoVariacao->getSomaTotalEstoque() . '</p>';

        $return = array(
            'html'      => $html,
            'retorno'   => 'success',
            'msg'       => 'Patrocinador confirmado.',
        );
    } else {
        $return = array(
            'html'      => '',
            'retorno'   => 'error',
            'msg'       => 'Variação excluída ou não encontrada.',
        );
    }
} else {
    $return = array(
        'html'      => '',
        'retorno'   => 'error',
        'msg'       => 'Variação ID inválido ou inexistente.',
    );
}


echo json_encode($return);
die;
