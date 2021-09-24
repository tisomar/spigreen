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
    $item = $object->getBuscapeShoppingItem();
    if (!($item instanceof BuscapeShoppingItem)) {
        $item = new BuscapeShoppingItem();
    }
}

if ($request->getMethod() == 'POST') {
    $dados = trata_post_array($request->request->get('data'));

    if ($dados['ATIVO'] == 1) {
        $item->setByArray($dados);
        $erros = array();

        if ($item->myValidate($erros)) {
            $item->save();
            $container->getSession()->getFlashBag()->add('success', 'Registro atualizado com sucesso!');
        } else {
            foreach ($erros as $erro) {
                $container->getSession()->getFlashBag()->add('error', $erro);
            }
        }
    } else {
        $item->delete();
        $container->getSession()->getFlashBag()->add('success', 'Registro removido com sucesso!');
    }
} else {
    if ($item->isNew()) {
        $container->getSession()->getFlashBag()->add('warning', 'Você ainda não salvou este registro!');
    }
}
