<?php

use QPress\Template\Widget;

ClearSaleMapper\Manager::set('page', 'home');

include_once __DIR__ . '/actions/index.action.php';
include_once QCOMMERCE_DIR . '/includes/head.php';
?>

<body itemscope itemtype="http://schema.org/WebPage" data-page="home">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.homepage.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<?php
$hoje = new Datetime();
$dataFinal = new Datetime('2019-11-23T12:00:00-05:00');

if ($hoje <= $dataFinal) :
    ?>
    <style type="text/css">
        .videoContainer {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .videoContainer iframe {
            /* optional */
            width: 100%;
            height: 100%;
        }

        #modalVideo {
            z-index: 10000001;
        }

        #modalVideo .modal-dialog {
            height: 90%;
            width: 95%;
            margin: 2.5%;
        }

        #modalVideo .modal-content {
            height: 100%;
        }

        #modalVideo .modal-header {
            height: 8%;
            position: absolute;
            width: 100%;
            z-index: 999;
        }

        #modalVideo .modal-header button {
            color: white;
        }

        #modalVideo .modal-body {
            height: 100%;
            width: 100%;
            position: absolute;
            top: 0;
        }

        .subscribe {
            position: absolute;
            left: 30px;
            opacity: 0.85;
            bottom: 70px;
        }
    </style>

    <div class="col-xs-12">
        <div class="modal fade" id="modalVideo" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="videoContainer">
                                <a class="btn btn-success subscribe" href="http://bit.ly/Future-se23-11" target="_blank">Inscreva-se</a>
                                <iframe
                                    width="560"
                                    height="315"
                                    src="https://www.youtube.com/embed/dpPSylpmKqY"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var lastDate = localStorage.getItem('lastDateVideoWatched');

        if (!lastDate || (new Date().getTime() - new Date(lastDate).getTime()) / 1000 / 60 > 30) {
            $(function() {
                setTimeout(function() {
                    $('#modalVideo')
                        .on('hide.bs.modal', function () {
                            $('.videoContainer iframe').attr('src', '');

                            const data = new Date();
                            localStorage.setItem('lastDateVideoWatched', data.toJSON());
                        })
                        .modal();
                }, 1000);
            });
        }
    </script>
    <?php
endif;
?>

<main role="main">
   
    <?php
    Widget::render('components/flash-messages');

    if ($franqueado && !$clienteLogado) :
        Widget::render('franqueado/apresentacao', ['franqueado' => $franqueado]);
    else :
        Widget::render('banners/home', [
            'collBanner' => $collBanner[BannerPeer::DESTAQUE],
            'banner_full' => Config::get('banner_full')
        ]);
        Widget::render('banners/vantagens', ['collBanner' => $collBanner[BannerPeer::VANTAGEM]]);
        Widget::render('banners/apoio', ['collBanner' => $collBanner[BannerPeer::APOIO]]);
    endif;
    ?>

    <div class='col-xs-12 col-sm-6 pull-right'>
        <?php Widget::render('forms/searchAdvancedItems'); ?>
    </div><br>
    
    <?php
    $limiteProdutosPorCategoria = Config::get('home_limite_quantidade_produtos');
    $limiteProdutosPorCategoria = (int)$limiteProdutosPorCategoria == 0 ? 8 : $limiteProdutosPorCategoria;

    foreach ($collCategorias as $objCategoria) :

        $subCategorias = array();
        $buscarProdutosDeSubcategorias = true;

        if ($buscarProdutosDeSubcategorias) :
            $subCategorias = $objCategoria->getBranch(
                CategoriaQuery::create()
                    ->select(array('Id'))
                    ->filterByDisponivel(true)
            )->toArray();
        endif;

        array_unshift($subCategorias, $objCategoria->getId());
        $ids = array('COMPOSTO', 'SIMPLES');
        $cids = implode(',', $ids);

        try {
            $collProdutos = ProdutoQuery::create()
                ->useProdutoCategoriaQuery()
                    ->filterByCategoriaId($subCategorias, Criteria::IN)
                ->endUse()
            ->orderByOrdem(Criteria::ASC)
            ->orderByValor(Criteria::ASC) // Existe no class ProductQuery
            ->filterByDisponivel(true)
            ->filterByDestaque(true)
            ->filterKits()
            ->groupById()
            ->limit($limiteProdutosPorCategoria)
                ->find();
        } catch (\PropelException $pe) {
            var_dump($pe);
        } catch (\Exception $e) {
            var_dump($e);
        }


        Widget::render('produto/product-list', array(
            'headingContainer' => array(
                'title' => htmlspecialchars($objCategoria->getNome()),
                'link' => $objCategoria->getUrl(),
                'urlTitle' => 'Ver mais produtos'
            ),
            'collProdutos' => $collProdutos,
            'carousel' => (Config::get('home_layout') == 'carousel'),
        ));
    endforeach;


    Widget::render('banners/rodape', array('collBanner' => $collBanner[BannerPeer::RODAPE]));
    Widget::render('banners/marcas');

    ?>

</main>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
</body>
</html>