<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 11/05/2018
 * Time: 17:58
 */

require_once QCOMMERCE_DIR . '/admin/includes/config.inc.php';
require_once QCOMMERCE_DIR . '/admin/includes/security.inc.php';

if (isset($_POST['produto_id']) && is_numeric($_POST['produto_id']) && $_POST['produto_id'] > 0) {
    $objProduto = ProdutoQuery::create()->findOneById($_POST['produto_id']);

    if ($objProduto instanceof Produto) {
        $variacao = ProdutoVariacaoQuery::create()->findByProdutoId($objProduto->getId());

        $html = get_form_select_object($variacao, '', 'getId', 'getSku', array('class' => 'form-control','name' => 'data[produto-composto-variacao-id][]'), array('' => 'Selecione')) ;

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
