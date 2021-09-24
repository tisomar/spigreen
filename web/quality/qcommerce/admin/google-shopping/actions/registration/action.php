<?php

/* @var $container \QPress\Container\Container */

$product = ProdutoPeer::retrieveByPK($_GET['reference']);
$request = $container->getRequest();

if ($request->query->has('reference')) {
    $object = ProdutoPeer::retrieveByPK($request->query->get('reference'));
}

if (!($object instanceof Produto)) {
    $container->getSession()->getFlashBag()->add('error', 'Desculpe-nos, mas por algum motivo as informações do produto foram perdidas');
    redirect_404admin();
} else {
    $gShop = $object->getGoogleShoppingItem();
    if (!($gShop instanceof GoogleShoppingItem)) {
        $gShop = new GoogleShoppingItem();
    }
}


if ($request->getMethod() == 'POST') {
    $dados = trata_post_array($request->request->get('data'));
    $gShop->setByArray($dados);
    $erros = array();

    if (is_null($gShop->getGoogleShoppingCategoria())) {
        $erros[] = "Você precisa associar este produto à uma categoria do google.";
    }

    if ($gShop->myValidate($erros)) {
        $gShop->save();
        $container->getSession()->getFlashBag()->add('success', 'Registro atualizado com sucesso!');
    } else {
        foreach ($erros as $erro) {
            $container->getSession()->getFlashBag()->add('error', $erro);
        }
    }
} else {
    if ($gShop->isNew()) {
        $container->getSession()->getFlashBag()->add('warning', 'Você ainda não salvou este registro!');
    }
}

//include QCOMMERCE_DIR . '/admin/_base/actions/' . $router->getAction() . '/action.php';
