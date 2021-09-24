<?php
require __DIR__ . '/actions/extrato-pontos.actions.php';

use QPress\Template\Widget;

$strIncludesKey = 'minha-conta-extrato-meus-pontos';
include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";
?>

<body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-extrato-pontos-rede">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<style>
    #bg-tot-pontos {
        background: #071f38;
        color: #FFF;
        font-weight: 700;
    }
</style>

<main role="main">
    <?php
    Widget::render(
        'components/breadcrumb',
        array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Extrato pontos de rede' => ''))
    );
    Widget::render('general/page-header', array('title' => 'Extrato pontos de rede'));
    Widget::render('components/flash-messages');
    ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-3">
                <?php Widget::render('general/menu-account', array('strIncludesKey' => $strIncludesKey)); ?>
            </div>
            <div class="col-xs-12 col-md-9">
                <div class="row">
                    <div class="col-sm-8">
                        <h3>
                            Extrato pontos de rede.
                        </h3>
                    </div>
                </div>
                <br>
                <form action="<?php echo get_url_site() .
                    '/minha-conta/extrato-pontos-rede/' ?>" role="form" method="get" class="form-disabled-on-load">
                    <?php
                        Widget::render(
                            'forms/filtro-pontos-rede',
                            [
                                'dtInicio' => $dtInicio,
                                'dtFim' => $dtFim,
                                'clientes' => $listaClientes,
                                'selectedClient' => $selectedCliente,
                                'listaPontosDescricao' => $listaPontosDescricao,
                                'selectedDescricao' => $descricaoPontos,
                                'maxGeracao' => $maxGeracao,
                                'filtroGeracao' => $filtroGeracao,
                                'dtPagamentoInicio' => $dtPagamentoInicio,
                                'dtPagamentoFim' => $dtPagamentoFim,
                            ]
                        );
                        
                    ?>
                    <div class="form-group">
                        <button type="submit" class="btn btn-theme btn-block">Filtrar</button>
                    </div>
                </form>

                <div class="row">
                    <div class="col-xs-12">
                        <?php if (count($list) > 0) : ?>
                           <div class="panel panel-default" id="bg-tot-pontos">
                               <div>
                                    <table style="color: white; border-spacing: 15px 10px; border-collapse: separate;">
                                        <tr>
                                            <td>
                                                <span class="<?php icon('info'); ?>"></span> TOTAL DE PONTOS ACUMULADOS
                                            </td>
                                            <td class="text-right">
                                                <strong><?php echo number_format($totalPontosAcumulados, 0, ',', '.')?></strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="<?php icon('info'); ?>"></span> TOTAL DE PONTOS RETIRADOS
                                            </td>
                                            <td class="text-right">
                                                <strong><?php echo number_format($totalPontosUtilizados, 0, ',', '.')?></strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="<?php icon('info'); ?>"></span> TOTAL DE PONTOS PERÍODO
                                            </td>
                                            <td class="text-right">
                                                <strong><?php echo number_format($totalPontosPeriodo, 0, ',', '.')?></strong>
                                            </td>
                                        </tr>
                                    </table>
                               </div>
                           </div>

                            <div class="table-vertical">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Pontos</th>
                                        <th class="text-left">Cliente</th>
                                        <th class="text-center">Geração</th>
                                        <th class="text-center">Pedido</th>
                                        <th class="text-center">Data</th>
                                        <th class="text-center">Pagamento</th>
                                        <th class="text-left">Descrição</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    foreach ($list as $pontos) : ?>
                                        <tr>
                                            <td class="text-center"><?= $pontos['PONTOS'] ?></td>
                                            <td class="text-left"><?= $pontos['CLIENTE'] ?></td>
                                            <td class="text-center">
                                                <?=
                                                    $pontos['GERACAO'] == 0
                                                        ? '-'
                                                        : $pontos['GERACAO']
                                                ?>
                                            </td>
                                            <td class="text-center"><?= $pontos['PEDIDO'] ?></td>
                                            <td class="text-center">
                                                <?=
                                                    $pontos['DATA_PEDIDO'] ?
                                                    date_create_from_format('Y-m-d H:i:s', $pontos['DATA_PEDIDO'])->format('d/m/Y') :
                                                    ''
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?=
                                                    $pontos['DATA_PAGAMENTO'] ?
                                                    date_create_from_format('Y-m-d H:i:s', $pontos['DATA_PAGAMENTO'])->format('d/m/Y') :
                                                    ''
                                                ?>
                                            </td>
                                            <td class="text-left"><?= $listaPontosDescricao[$pontos['DESCRICAO']] ?? '' ?></td>
                                        </tr>
                                        <?php
                                    endforeach;
                                    ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php
                            if (true): /* @var $pager PropelModelPager */
                                $href = get_url_site() . '/minha-conta/extrato-pontos-rede/';

                                $queryString = '?' . $request->getQueryString();

                                if (!isset($queryString) || in_array($queryString, ['?', '/?', '/'])) :
                                    $queryString = "";
                                endif;

                                $toFirst = $href . $firstPage . $queryString;
                                $toLast = $href . $lastPage . $queryString;

                                $toPrev = $href . $prevPage . $queryString;
                                $toNext = $href . $nextPage . $queryString;
                                ?>
                                <div class="align-center">
                                    <p>
                                        Resultado(s) <?= $offset + 1; ?> - <?= min($countResults, $offset + $limit); ?> de <?= $countResults; ?>
                                    </p>
                                    <ul class="pagination">
                                        <li><a href="<?php echo $toFirst ?>" title="Primeira página"><span class="<?php icon('angle-double-left'); ?>"></span></a></li>
                                        <li><a href="<?php echo $toPrev ?>" title="Página anterior" class="<?php icon('angle-left'); ?>"></a></li>

                                        <?php for ($i = $minPage; $i <= $maxPage; $i++): ?>
                                            <li class="<?php echo $i == $page ? 'active' : '' ?>">
                                                <a href="<?php echo $href . $i . $queryString; ?>">
                                                    <?php echo $i ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>

                                        <li><a href="<?php echo $toNext ?>"  title="Próxima página" class="<?php icon('angle-right'); ?>"></a></li>
                                        <li><a href="<?php echo $toLast ?>" title="Última página" class="<?php icon('angle-double-right'); ?>"></a></li>
                                    </ul>
                                </div>
                                <?php
                            endif;
                            ?>
                        <?php else : ?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <span class="<?php icon('info'); ?>"></span> Nenhum extrato encontrado!
                                        </div>
                                    </div>
                                </div>
                                <br>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>
</body>

</html>