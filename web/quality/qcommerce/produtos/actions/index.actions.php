<?php
use QPress\Template\Widget;

$breadcrumb = array();

// $produtoCriteria = ProdutoQuery::create();
$produtoCriteria = ProdutoQuery::create()->useEstoqueProdutoQuery()->enduse();

if ($router->getArgument(0) != '' && !is_numeric($router->getArgument(0))) {
    $page = (int) $router->getArgument(1) > 0 ? (int) $router->getArgument(1) : 1;

    if ($router->getArgument(0) == 'marca') {
        $objMarca = MarcaQuery::create()->findOneByKey($router->getArgument(1));
        if (is_null($objMarca)) {
            redirect_404();
        }

        $url    = ROOT_PATH . '/produtos/marca/' . $router->getArgument(1) . '/';
        $tituloPagina       = $objMarca->getNome();

        $produtoCriteria->filterByMarca($objMarca);

        $breadcrumb = array('Marcas' => '/marcas', $objMarca->getNome() => '');
    }
    /**
     * Procura os produtos com base na categoria.
     */
    else {
        # Busca a categoria pela chave definida na URL
        $objCategoria = CategoriaQuery::create()->findOneByKey(filter_var(trim($router->getArgument(0)), FILTER_SANITIZE_STRING));

        # Caso não encontre mais a categoria, redireciona o cliente à página de erro 404
        if (is_null($objCategoria)) {
            // Pode ser que seja a página de produtos com um franqueado, ex: oxi.../produtos/sandroluiz
            if (!validaSlug($router->getArgument(0), $container, 1, 'produtos')) {
                redirect_404();
            } else {
                $url = ROOT_PATH . '/produtos/';
            }
        }

        # Verifica e continua se a categoria acessada existir
        elseif (!is_null($objCategoria)) {
            # Verifica se a categoria está disponível para ser acessada
            if ($objCategoria->isDisponivel() == false) {
                FlashMsg::info('Esta categoria está indisponível no momento.');
                redirectTo(get_url_site());
            }

            # Define a página atual para a paginação
            $page = (int) $router->getArgument(1) > 0 ? (int) $router->getArgument(1) : 1;
            
            //Se está informado o número da página, o slug vai estar na terceira posição do array
            if ((int) $router->getArgument(1) > 0) {
                $slug = $router->getArgument(2);
                validaSlug($slug, $container, $page);
            } else {
                $slug = $router->getArgument(1);
                validaSlug($slug, $container);
            }
            
            # Define a URL base para a paginação
            $url = ROOT_PATH . '/produtos/' . $objCategoria->getKey() . '/';

            # Busca as categorias antecessoras para montar o breadcrumb
            $subCategorias = $objCategoria->getChildren(
                CategoriaQuery::create()
                    ->select(array('Id'))
            )->toArray();

            array_unshift($subCategorias, $objCategoria->getId());

            $produtoCriteria->useProdutoCategoriaQuery()
                ->filterByCategoriaId($subCategorias, Criteria::IN)
                ->endUse();

            # Busca as categorias antecessoras para montar o breadcrumb
            $_categoriasAntecessoras = $objCategoria->getAncestors(
                CategoriaQuery::create()
                    ->select(array('Nome', 'Key'))
                    ->filterByNrLvl(0, Criteria::GREATER_THAN)
                    ->filterByParentDisponivel(true)
                    ->filterByDisponivel(true)
            )->toArray();

            foreach ($_categoriasAntecessoras as $aCategoria) {
                $breadcrumb[$aCategoria['Nome']] = '/produtos/' . $aCategoria['Key'] . '/';
            }

            # Adiciona a categoria no breadcrumb
            $breadcrumb[$objCategoria->getNome()] = '';
        }
    }
} else {
    # Página de produtos sem filtros por blocos da home ou categorias
    $page = (int) $router->getArgument(0) > 0 ? (int) $router->getArgument(0) : 1;
    if ((int) $router->getArgument(0) > 0) {
        $slug = $router->getArgument(1);
        validaSlug($slug, $container, $page);
    } else {
        validaSlug(null, $container);
    }
    
    $url    = ROOT_PATH . '/produtos/';
    $breadcrumb['Produtos'] = '';
}

# caso esteja em uma paginação, desabilita a indexacao desta página
if ($page > 1) {
    $meta['noindex'] = false;
}


# Busca os produtos levando em consideração a ordenação e os filtros da página
$collProdutos = ProdutoPeer::findAll($produtoCriteria, array (
    'ordenar-por'   => $session->get('ordenar-por'),
    'max-per-page'  => $session->get('produtos-por-pagina'),
    'page'          => $page,
    'categoria'     => $objCategoria->getKey()
));

# Insere o link da home como primeiro no breadcrumb
$breadcrumb = array_merge(array('Home' => '/home'), $breadcrumb);

function validaSlug($slug, $container, $page = null, $pagina = null)
{
    if (!is_null($slug)) {
        $objHotsite = HotsiteQuery::create()
            ->filterBySlug($slug)
            ->findOne();
        
        if (!$objHotsite instanceof Hotsite) {
            redirect_404();
        }
        
        $container->getSession()->set('fromFranqueado', true);
        $container->getSession()->set('slugFranqueado', $slug);
        return true;
    } elseif ($container->getSession()->has('fromFranqueado')) {
        if (!is_null($page)) {
            if (!is_null($pagina)) {
                redirectTo($container->getRequest()->getRequestUri() . $pagina . '/' . $page . '/' . $container->getSession()->get('slugFranqueado'));
            }
            redirectTo($container->getRequest()->getRequestUri() . $page . '/' . $container->getSession()->get('slugFranqueado'));
        }
        redirectTo($container->getRequest()->getRequestUri() . $container->getSession()->get('slugFranqueado'));
    }
    return false;
}
