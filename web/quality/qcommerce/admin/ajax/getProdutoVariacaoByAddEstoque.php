<?php

if (isset($_POST['produto_id']) && is_numeric($_POST['produto_id']) && $_POST['produto_id'] > 0) {
    $objProduto = ProdutoQuery::create()->findOneById($_POST['produto_id']);

    if ($objProduto instanceof Produto) {
        $variacao = ProdutoVariacaoQuery::create()->findByProdutoId($objProduto->getId());
        $defaultValue = isset($_SESSION['PRODUTO_VARIACAO_ESTOQUE']) ? $_SESSION['PRODUTO_VARIACAO_ESTOQUE'] : '';

        if (isset($_SESSION['PRODUTO_VARIACAO_ESTOQUE'])) {
            unset($_SESSION['PRODUTO_VARIACAO_ESTOQUE']);
        }

        $html = get_form_select_object(
            $variacao,
            $defaultValue,
            'getId',
            'getSku',
            array('class' => 'form-control','name' => 'data[PRODUTO_VARIACAO_ID]', 'id' => 'produto-variacao')
        ) ;

        $return = array(
            'html'      => $html,
            'retorno'   => 'success',
            'msg'       => 'Patrocinador confirmado.',
        );
    } else {
        $return = array(
            'html'      => '',
            'retorno'   => 'error',
            'msg'       => 'Produto excluído ou não encontrado.',
        );
    }
} else {
    $return = array(
        'html'      => '',
        'retorno'   => 'error',
        'msg'       => 'Produto ID inválido ou inexistente.',
    );
}


echo json_encode($return);
die;
