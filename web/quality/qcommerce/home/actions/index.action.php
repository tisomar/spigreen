<?php

$strIncludesKey = 'home';
$url            = ROOT_PATH . '/home/';

/**
 * Busca os banners destaques
 */
$collBanner[BannerPeer::DESTAQUE] = BannerQuery::findBannerByType(BannerPeer::DESTAQUE);

/**
 * Busca os banners de vantagens
 */
$collBanner[BannerPeer::VANTAGEM] = BannerQuery::findBannerByType(BannerPeer::VANTAGEM, 3);

/**
 * Busca Banners de Apoio ao Banner Principal
 */
$collBanner[BannerPeer::APOIO] = BannerQuery::findBannerByType(BannerPeer::APOIO, 3);

/**
 * Busca Banners de Apoio do Rodapé
 */
$collBanner[BannerPeer::RODAPE] = BannerQuery::findBannerByType(BannerPeer::RODAPE, 3);

/**
 * Busca as categorias definidas que devem aparecer na homepage
 */

$clienteLogado = ClientePeer::getClienteLogado(true);

$reseller = $container->getSession()->has('resellerLoggedActive');

$categoriaCombo = ($clienteLogado && $clienteLogado->getPlanoId() > 0) ? true : $reseller ? true : false;

$collCategorias = CategoriaQuery::create()
    ->filterByMostrarPaginaInicial(true)
    ->filterByDisponivel(true)
    ->filterByParentDisponivel(true)
    ->_if(!$categoriaCombo)
        ->filterByCombo(false)
    ->_endif()
    ->_if(Config::get('mostrar_todas_categorias') == 0)
    ->add('1', CategoriaPeer::queryCategoriasComProdutosAtivos(), Criteria::CUSTOM)
    ->addOr('2', CategoriaPeer::queryProdutosAtivos(), Criteria::CUSTOM)
    ->_endif()
    ->orderByOrdem()
    ->find();

$franqueado = null;

$isHotsiteCliente = !is_null($clienteLogado)
    && is_null($clienteLogado->getPlanoId())
    && !is_null($clienteLogado->getClienteIndicadorId());

if ($isHotsiteCliente) :
    $franqueado = HotsiteQuery::create()
        ->filterByClienteId($clienteLogado->getClienteIndicadorId())
        ->findOne();
elseif ($container->getSession()->has('slugFranqueado') && is_null($clienteLogado)) :
    $franqueado = HotsiteQuery::create()
        ->filterBySlug($container->getSession()->get('slugFranqueado'))
        ->findOne();
endif;
