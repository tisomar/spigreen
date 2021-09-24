<?php
use QPress\Frete\Package\PackageClient;

$chave = isset($args[0]) ? $args[0] : '';
$chave = filter_var(trim($chave), FILTER_SANITIZE_STRING);

$condition = ProdutoQuery::create()->filterByKey($chave);
$objProdutoDetalhe = ProdutoPeer::findOneBy($condition);

if (!$objProdutoDetalhe instanceof Produto) {
    redirect_404();
}

if (isset($args[1])) {
    $slug = $args[1];
    $objHotsite = HotsiteQuery::create()
        ->filterBySlug($slug)
        ->findOne();
    
    if (!$objHotsite instanceof Hotsite) {
        redirect_404();
    }
    
    $container->getSession()->set('fromFranqueado', true);
    $container->getSession()->set('slugFranqueado', $slug);
} elseif ($container->getSession()->has('fromFranqueado')) {
    redirectTo($container->getRequest()->getRequestUri() . $container->getSession()->get('slugFranqueado'));
}

/*
 Agora o slug é gravado na sessao na pagina "franquado/index"

$lastUrl        = $container->getRequest()->server->get('HTTP_REFERER');
$posFranqueado  = strpos($lastUrl,'franqueado');
$slugFranqueado = substr($lastUrl,$posFranqueado+strlen('franqueado/'),strlen($lastUrl));

if($posFranqueado !== false){
    $container->getSession()->set('fromFranqueado',true);
    $container->getSession()->set('slugFranqueado',$slugFranqueado);
 */

# Não permite mostrar o produto se todas as categorias que ele pertence estiverem inativas.
$possuiCategoriaAtiva = (bool) $objProdutoDetalhe
        ->getProdutoCategoriasJoinCategoria(
            CategoriaQuery::create()
                ->filterByDisponivel(true)
                ->filterByParentDisponivel(true)
        )->count() > 0;

if ($possuiCategoriaAtiva == false) {
    FlashMsg::info('O produto que você tentou acessar não está disponível');
    redirectTo(get_url_site());
}


# Recurso comentado porque não temos disponíveis essa informação.
/*if (ClientePeer::isAuthenticad()) {
    ClientePeer::produtoVisitado($objProdutoDetalhe);
}*/

$objComentarios = $objProdutoDetalhe->getProdutoComentarioAprovado(3);
$objProdRelacionados = $objProdutoDetalhe->getProdutosRelacionados(4);

$breadcrumb[resumo(escape($objProdutoDetalhe->getNome()), 18, '')] = '';
$breadcrumb = array_merge(array('Home' => '/home', 'Produtos' => '/produtos'), $breadcrumb);

/**
 * Consulta de Frete
 */

include_once QCOMMERCE_DIR . '/carrinho/actions/cep.actions.php';
$address = $frete = $calculated = null;
if ($container->getSession()->has('CEP_SIMULACAO')) {
    # Consulta o endereço no site dos correios para saber se é um CEP válido para consultar o frete.
    $address = \QPress\Correios\CorreiosEndereco::consultaCepViaCep($container->getSession()->get('CEP_SIMULACAO'));
    if (!is_null($address)) {
        # Cria o pacote com as informações de peso e dimensão do produto para calcular o frete
        $package = $objProdutoDetalhe->generatePackage($container->getSession()->get('CEP_SIMULACAO'));
        # Calcula o frete disponível para este pacote
        $frete = \QPress\Frete\Manager\FreteManager::calcularFreteCompleto($package);
        $calculated = true;
    } else {
        if ($container->getRequest()->getMethod() == 'POST' && $container->getRequest()->request->get('CEP')) {
            FlashMsg::add('danger', 'Não foi possível consultar o frete com o CEP informado. Por favor, verifique se o mesmo foi digitado corretamente.');
        }
    }
}
