<?php
use QPress\Template\Widget;
include_once __DIR__ . '/actions/index.actions.php';

ClearSaleMapper\Manager::set('page', 'search');
ClearSaleMapper\Manager::set('description', 'key-words=' . escape($busca) . ', page-number=' . intval($page));

include_once QCOMMERCE_DIR . '/includes/head.php';
?>

<body itemscope itemtype="http://schema.org/SearchResultsPage">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php if (isset($busca) && $busca != '') : ?>
    <?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.search.tracking.php'; ?>
<?php else : ?>
    <?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php endif; ?>
<?php Widget::render('general/header'); ?>

<main role="main">
    <?php Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Busca' => ''))); ?>

    <?php if (count($collProdutos)) : ?>
        <?php 
        
        $titlePesquisa = '';

        if (!empty($busca)): 
            $titlePesquisa = 'Resultados para "' . resumo($busca, 32) . '"';
        endif;
        if (!empty($ordenacao)): 
            switch($ordenacao) {
                case 'preco-asc':
                    $title = 'Menor valor';
                    break;
                case 'mais-vendidos';
                    $title = 'Mais vendidos';
                    break;
                case 'melhor-avaliados';
                    $title = 'Avaliação';
                    break;
                case 'nome-asc';
                    $title = 'Ordem Alfabetica';
                    break;
            }
            $titlePesquisa = 'Resultados para "' . resumo($title, 32) . '"';
        endif;

        if (!empty($busca) && !empty($ordenacao)):
            $titlePesquisa = 'Resultados para "' . resumo($busca, 32) . '" e "' . resumo($title, 32) . '"';
        endif; 

        Widget::render('general/page-header', array('title' => $titlePesquisa));
        Widget::render('components/flash-messages');
        ?>
        <div class="container">
            <?php include_once QCOMMERCE_DIR . '/produtos/components/filtro.php'; ?>

            <?php
            Widget::render('produto/product-list', array(
                'collProdutos' => $collProdutos,
                'href'  => $url,
                'queryString' => '?buscar-por=' . $request->query->get('buscar-por'),
                'align' => 'center'
            ));
            /*Widget::render('components/pagination', array(
                'pager' => $collProdutos,
                'href'  => get_url_site() . '/busca/',
                'queryString' => '/?buscar-por=' . $request->query->get('buscar-por'),
                'align' => 'center'
            ));*/
            ?>
        </div>

    <?php else : ?>
        <?php
        Widget::render('general/page-header', array('title' => 'Sua busca por "' . resumo($busca, 32) . '" não retornou nenhum resultado.'));
        Widget::render('components/flash-messages');
        ?>
        <div class="container">
            <p>
                Verifique se não há erro de digitação.<br>
                Tente utilizar uma única palavra.<br>
                Tente buscar por termos menos espefíficos e posteriormente use os filtros da busca.<br>
                Procure utilizar sinônimos ao termo desejado.
            </p>
        </div>

    <?php endif; ?>
</main>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
</body>
</html>
