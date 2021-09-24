<?php

$router = new \QPress\Router\RouteResolver($request);

$mapUrl['semRegistro'] = array(
    'home' => SeoPeer::PAGINA_HOME,
    'contato' => SeoPeer::PAGINA_CONTATO,
    'empresa' => SeoPeer::PAGINA_EMPRESA,
    'perguntas-frequentes' => SeoPeer::PAGINA_FAQ,
    'carrinho' => SeoPeer::PAGINA_CARRINHO,
    'login' => SeoPeer::PAGINA_LOGIN,
    'cadastro' => SeoPeer::PAGINA_CADASTRO,
);

if (isset($mapUrl['semRegistro'][$router->getModule()])) {
    $objSeo = SeoQuery::create()->findOneByPagina($mapUrl['semRegistro'][$router->getModule()]);
} else {
    $pagina = SeoPeer::PAGINA_PRODUTO;
    $registro_id = null;

    if ($router->getModule() == 'produtos') {
        if ($router->getAction() == 'detalhes') {
            if (isset($objProdutoDetalhe)) {
                $registro_id = $objProdutoDetalhe->getId();
            }
        } elseif ($router->getAction() == 'promocoes') {
            $pagina = SeoPeer::PAGINA_PROMOCAO;
        } else {
            // Verifica se está listando por categoria
            if (isset($objCategoria) && !is_null($objCategoria)) {
                $pagina = SeoPeer::PAGINA_CATEGORIA;
                $registro_id = $objCategoria->getId();
            }
        }
    }

    $objSeo = SeoQuery::create()
            ->filterByPagina($pagina)
            ->filterByRegistroId($registro_id)
            ->_or()
            ->filterByRegistroId(0)
            ->orderByRegistroId(Criteria::DESC)
        ->findOne();
}


if (isset($objSeo) && $objSeo instanceof Seo) {
    $strTitle = Config::get("empresa_nome_fantasia") . ' - ' . $objSeo->getMetaTitle();
    $strDescription = $objSeo->getMetaDescription();
    $strKeyWord = $objSeo->getMetaKeywords();
} else {
    $strTitle = Config::get("empresa_nome_fantasia");
    $strDescription = Config::get("empresa_nome_fantasia");
    $strKeyWord = "";
}
