<?php

use QPress\Template\Widget;

require __DIR__ . '/actions/plano-carreira.actions.php';

include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";

// INCLUDES PARA O GRÁFICO
//include QCOMMERCE_DIR . '/admin/relatorio/helpers/js.php';
//include QCOMMERCE_DIR . '/admin/relatorio/helpers/functions.php';

$strIncludesKey = 'minha-conta-plano-carreira';

$showMenu = !isset($showMenu) ? true : $showMenu;

$ticks = [];
// $startDate->__wakeup();
$start = clone $startDate;
$i = 1;
$dataValorVenda = [];

while ($start < $endDate) :
    $ticks[] = [$i++, $start->format('d')];
    $dataValorVenda[$start->format('d') - 1] = [$start->format('d'), 0];
    $start->modify('+1 day');
endwhile;

?>
<body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-meu-plano">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<main role="main">
    <?php
    Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Plano de Carreira' => '')));
    Widget::render('general/page-header', array('title' => 'Plano de Carreira'));
    Widget::render('components/flash-messages');
    ?>
    <style>
        .alert {
            position: relative;
            padding: .75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: .25rem;
        }

        .alert-primary {
            color: #004085;
            background-color: #cce5ff;
            border-color: #b8daff;
        }

        .boxgraduacao {
            margin: 2vmin;
            border: 1px solid #ccc !important;
            border-radius: 16px;
            padding: 2vmin;
            min-height: 25vmin;
        }

        .boxgraduacao img {
            margin-top: 2vmin;
        }

        .anychart {
            width: 100%;
            height: 50vmin;
            margin: 0;
            padding: 0;
        }

        .barra-progresso-plano-carreira {
            border-radius: 4px;
            border: 2px solid #4CAF50;
            height: 25px;
            font-weight: bold;
        }

        .barra-progresso-plano-carreira div {
            background-color: #4CAF50;
        }
    </style>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-3">
                <?php Widget::render('general/menu-account', array('strIncludesKey' => $strIncludesKey)); ?>
            </div>
            <div class="col-xs-12 col-md-9">
                <div class='col-sm-12'>
                    <div class="row">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4>
                                    <?= $nomePlano; ?>
                                </h4>
                                <!-- <h4>-->
                                <!-- Seu Plano: --><? //= $nomePlano ?>
                                <!-- </h4>-->

                            </div>
                        </div>
                    </div>
                    <!-- CODIGO COPIADO -->
                    <div class="row">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4>Configurações</h4>
                            </div>
                            <div class="panel-body">
                                <table class="table hidden-sm">
                                    <thead>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="2">Meu código de indicação:</td>
                                        <td class="text-left">
                                            <span>Codigo:</span><br>
                                            <b><?= $clienteLogado->getChaveIndicacao(); ?></b>
                                        </td>
                                        <td>
                                            <span>Link de indicação:</span><br>
                                            <button
                                                    class="btn btn-primary"
                                                    id="link-patrocinador"
                                                    onclick="Clipboard.copy('<?= $linkIndicacao ?>')"
                                                    data-link="<?= $linkIndicacao ?>"
                                            >Clique para Copiar</button>
                                        </td>
                                    </tr>
                                    <?php if (!empty($patrocinadorNome) && !empty($patrocinadorChave)) : ?>
                                        <tr>
                                            <td colspan="2">Patrocinador vinculado:</td>
                                            <td>
                                                <span>Código:</span><br>
                                                <?= $patrocinadorChave  ?>
                                            </td>
                                            <td>
                                                <span>Nome:</span><br>
                                                <?= $patrocinadorNome ?>
                                            </td>
                                            <td></td>
                                        </tr>
                                    <?php endif ?>
                                    <tr>
                                        <td colspan="2">Total de participantes da rede:</td>
                                        <td class="text-left">
                                            <b><?php echo $clienteLogado->getTotalParticipantesRede() ?></b>
                                        </td>
                                        <td></td>
                                    </tr>
                                    </tbody>
                                </table>
                                <table class="table visible-sm">
                                    <tbody>
                                    <tr>
                                        <td>Meu código de indicação:</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">
                                            <b><?= $clienteLogado->getChaveIndicacao(); ?></b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">Link de indicação:</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">
                                            <button
                                                    class="btn btn-primary btn-sm btn-block"
                                                    id="link-patrocinador"
                                                    onclick="Clipboard.copy('<?= $linkIndicacao ?>')"
                                                    data-link="<?= $linkIndicacao ?>"
                                            >Clique para Copiar</button>
                                        </td>
                                    </tr>
                                    <?php if (!empty($patrocinadorNome) && !empty($patrocinadorChave)) : ?>
                                        <tr>
                                            <td>Patrocinador vinculado:</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">
                                                <?= $patrocinadorChave?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">
                                                <?= $patrocinadorNome ?>
                                            </td>
                                        </tr>
                                    <?php endif ?>
                                    <tr>
                                        <td>Total de participantes da rede: </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">
                                            <b><?= $clienteLogado->getTotalParticipantesRede() ?></b>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- CODIGO COPIADO -->
                </div>

                <?php if (/*$planoAtual*/
                    1 > 2) : ?>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="progress barra-progresso-plano-carreira">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                     role="progressbar"
                                                     style="width: <?= $percentualProgresso ?>%;"
                                                     aria-valuenow="<?= $percentualProgresso ?>"
                                                     aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row text-center">
                                            <?php echo $percentualProgresso . "% ({$totalPontosAtual} pontos)"; ?>
                                        </div>
                                        <div class="row form-group">
                                            <div class="col-xs-6">
                                                <h6><?= $planoAtual->getPontos() ?> pontos</h6>
                                                <?= $planoAtual->getGraduacao() ?>
                                                <img src="<?php echo asset('/admin/arquivos/' . $planoAtual->getImagem()) ?>"
                                                     width='50vmin' class="card-img-top pull-center" alt="...">
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <h6><?= $proximoPlano->getPontos() ?> pontos</h6>
                                                <?= $proximoPlano->getGraduacao() ?>
                                                <img src="<?php echo asset('/admin/arquivos/' . $proximoPlano->getImagem()) ?>"
                                                     width='50vmin' class="card-img-top pull-center" alt="...">
                                            </div>
                                        </div>
                                        <?php if ($menssagemRequisitos != null) : ?>
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="alert alert-warning" role="alert">
                                                        <?php echo $menssagemRequisitos ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="table-vertical">
                                    <div class='row col-sm-12'>

                                        <div class="col-sm-5 col-xs-12">
                                            <h3>Relatório » Plano de carreira</h3>
                                        </div>

                                    </div>

                                    <form class="form-inline periodo" action="">

                                        <div class="form-group">
                                            <select class="form-control" id="mes" name="mes">
                                                <?php
                                                foreach ($meses as $value => $mes) :
                                                    $selected = $mesSelecionado == $value ? 'selected' : '';
                                                    echo "<option $selected value=\"$value\">$mes</option>";
                                                endforeach;
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select class="form-control" id="ano" name="ano">
                                                <option value="">Selecione o Ano</option>
                                                <?php
                                                foreach ($listaAno as $ano) :
                                                    $selected = $anoSelecionado == $ano ? 'selected' : '';
                                                    echo "<option $selected value=\"$ano\">$ano</option>";
                                                endforeach;
                                                ?>
                                            </select>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Filtrar</button>
                                    </form>
                                    <?php
                                    $groupByOptions = array(
                                        'month' => 'MES, ANO',
                                        'day' => 'DIA, MES, ANO',
                                        'hour' => 'HORA, DIA, MES, ANO',
                                    );
                                    if ($startDate->diff($endDate)->days == 0) :
                                        $groupBy = "hour";
                                    elseif ($startDate->diff($endDate)->days <= 30) :
                                        $groupBy = "day";
                                    else :
                                        $groupBy = 'month';
                                    endif;

                                    $totais = PedidoQuery::create()
                                        ->useClienteQuery()
                                        ->filterByTreeLeft($clienteLogado->getTreeLeft(), Criteria::GREATER_EQUAL)
                                        ->filterByTreeRight($clienteLogado->getTreeRight(), Criteria::LESS_EQUAL)
                                        ->endUse()
                                        ->usePedidoStatusHistoricoQuery()
                                        ->filterByPedidoStatusId(1)
                                        ->filterByIsConcluido(1)
                                        ->endUse()
                                        ->select(['DIA', 'TOTAL'])
                                        ->withColumn(sprintf('DAY(%s)', PedidoPeer::CREATED_AT), 'DIA')
                                        ->withColumn(sprintf('IFNULL(SUM(%s), 0)', PedidoPeer::VALOR_PONTOS), 'TOTAL')
                                        ->filterByStatus(Pedido::CANCELADO, Criteria::NOT_EQUAL)
                                        ->filterByCreatedAt(['min' => $startDate, 'max' => $endDate])
                                        ->addGroupByColumn(sprintf('DAY(%s)', PedidoPeer::CREATED_AT))
                                        ->orderByCreatedAt()
                                        ->find();

                                    $totalizadores = [
                                        'TOTAL' => 0,
                                        'PP' => 0,
                                        'PA' => 0,
                                        'PR' => 0
                                    ];

                                    $dias = [];
                                    $pontos = [];

                                    foreach ($totais as $total) :
                                        $dia = $total['DIA'];
                                        $total = $total['TOTAL'];

                                        $dataValorVenda[$dia - 1][1] = $total;
                                        $dataValorVenda[$dia - 1][2] = sprintf(
                                            '%s-%s-%s',
                                            $startDate->format('Y'),
                                            $startDate->format('m'),
                                            $dia
                                        );

                                        $totalizadores['TOTAL'] += $total;

                                        if (!in_array($dia, $dias)):
                                            $dias[] = $dia;
                                            $pontos[] = [$dia, $total];
                                        endif;
                                    endforeach;
                                    ?>
                                    <?php if (!$statusCliente): ?>
                                        <h5>
                                            <span class="alert alert-danger col-xs-12"> Status atual: <?= 'Inativo' ?></span>
                                        </h5>
                                    <?php else: ?>
                                        <h5>
                                            <span class="alert alert-success col-xs-12"> Status atual: <?= 'Ativo' ?></span>
                                        </h5>
                                    <?php endif ?>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class='col-sm-12 pull-center'>
                                                            <div class='col-sm-4'>
                                                                <div class="card text-center boxgraduacao">
                                                                    <span class="alert alert-warning col-xs-12">Graduação anterior</span>
                                                                    <?php if ($objImagemGraduacaoAnterior != null): ?>
                                                                        <img src="<?php echo asset('/admin/arquivos/' . $objImagemGraduacaoAnterior) ?>"
                                                                             width='50vmin'
                                                                             class="card-img-top pull-center" alt="...">
                                                                    <?php endif ?>
                                                                    <div class="card-body">
                                                                        <h5 class="card-title">
                                                                            <strong> <?php echo $graduacaoAnterior ?></strong>
                                                                        </h5>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class='col-sm-4'>
                                                                <div class="card text-center boxgraduacao">
                                                                    <span class="alert alert-success col-xs-12">Sua graduação</span>
                                                                    <?php if ($objImagemGraduacaoAtual != null): ?>
                                                                        <img src="<?php echo asset('/admin/arquivos/' . $objImagemGraduacaoAtual) ?>"
                                                                             width='50vmin'
                                                                             class="card-img-top pull-center" alt="...">
                                                                    <?php endif ?>
                                                                    <div class="card-body">
                                                                        <h5 class="card-title">
                                                                            <strong><?php echo $graduacaoAtual ?></strong>
                                                                        </h5>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class='col-sm-4'>
                                                                <div class="card text-center boxgraduacao">
                                                                    <span class="alert alert-primary col-xs-12">Maior graduação</span>
                                                                    <?php if ($objImagemMaiorGraduacao != null): ?>
                                                                        <img src="<?php echo asset('/admin/arquivos/' . $objImagemMaiorGraduacao) ?>"
                                                                             width='50vmin'
                                                                             class="card-img-top pull-center" alt="...">
                                                                    <?php endif ?>
                                                                    <div class="card-body">
                                                                        <h5 class="card-title">
                                                                            <strong><?php echo $maiorGraduacao ?></strong>
                                                                        </h5>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="report-printable">
                                            <div class="col-xs-12 col-md-6 col-lg-3">
                                                <h3>
                                                    <?php echo $PP ?>
                                                    <small class="text-muted">PP: Pontuação pessoal</small>
                                                </h3>
                                            </div>
                                            <div class="col-xs-12 col-md-6 col-lg-3">
                                                <h3>
                                                    <?php echo $PA ?>
                                                    <small class="text-muted">
                                                        PA: Pontuações de adesão
                                                    </small>
                                                </h3>
                                            </div>
                                            <div class="col-xs-12 col-md-6 col-lg-3">
                                                <h3>
                                                    <?php echo $PR ?>
                                                    <small class="text-muted">
                                                        PR: Pontuação de recompra
                                                    </small>
                                                </h3>
                                            </div>
                                            <div class="col-xs-12 col-md-6 col-lg-3">
                                                <h3>
                                                    <?php echo $totalPontos ?>
                                                    <small class="text-muted">
                                                        Total de pontos no período
                                                    </small>
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12" style="margin-top: 15px;">
                                        <div class="row">
                                            <small class="text-muted">
                                                Obs.: Os pontos de carreira são calculados utilizando o horário de
                                                Brasília.
                                            </small>
                                        </div>
                                    </div>
                                    <!--<div class="col-xs-12 hidden-xs">-->
                                    <!--    <div class="panel panel-gray">-->
                                    <!--        <div class="panel-heading row">-->
                                    <!--            <h4 class='text-center'> Graduação no período de -->
                                    <?php //echo $meses[$mesSelecionado] ?><!-- <span class='alert alert-primary' id='infoProntos' hidden> </span> </h4>-->
                                    <!--        </div>-->
                                    <!--        <div class="panel-body">-->
                                    <!--            <div id="site-statistics" style="height:380px;">-->
                                    <!--                <span id='sp1'></span>-->
                                    <!--            </div>-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <!--<div class="col-xs-12 visible-xs">-->
                                    <!--    <div class="report-printable">-->
                                    <!--        <div class="table-responsive ">-->
                                    <!--            <table class="table">-->
                                    <!--                <thead>-->
                                    <!--                <tr>-->
                                    <!--                    <th class="text-left">Período</th>-->
                                    <!--                    <th class="text-right">Pontos</th>-->
                                    <!--                </tr>-->
                                    <!--                </thead>-->
                                    <!--                <tbody>-->
                                    <!--                --><?php //foreach ($dataValorVenda as $i => $data) : ?>
                                    <!--                    <tr>-->
                                    <!--                        <td data-title="Período" class="text-left">-->
                                    <?php //echo strip_tags($ticks[$i][1]); ?><!--</td>-->
                                    <!--                        <td data-title="Pontos" class="text-right">-->
                                    <?php //echo $data[1]; ?><!--</td>-->
                                    <!--                    </tr>-->
                                    <!--                --><?php //endforeach; ?>
                                    <!--                </tbody>-->
                                    <!--                <tfoot>-->
                                    <!--                <tr>-->
                                    <!--                    <td data-title="" class="text-right"><b>Total:</b></td>-->
                                    <!--                    <td data-title="" class="text-right"><b> -->
                                    <?php //echo number_format($totalPontos, 0, ',', '.') ?><!--</b></td>-->
                                    <!--                </tr>-->
                                    <!--                </tfoot>-->
                                    <!--            </table>-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <?php
                                    //echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/charts-flot/jquery.flot.min.js'));
                                    //echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/charts-flot/jquery.flot.resize.min.js'));
                                    //echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/charts-flot/jquery.flot.orderBars.min.js'));
                                    //echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/form-daterangepicker/moment.min.js'));
                                    //echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/plugins/form-daterangepicker/daterangepicker.min.js'));
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script type="text/javascript">
    window.Clipboard = (function (window, document, navigator) {
        var textArea,
            copy;

        function isOS() {
            return navigator.userAgent.match(/ipad|iphone/i);
        }

        function createTextArea(text) {
            textArea = document.createElement('textArea');
            textArea.value = text;
            document.body.appendChild(textArea);
        }

        function selectText() {
            var range,
                selection;

            if (isOS()) {
                range = document.createRange();
                range.selectNodeContents(textArea);
                selection = window.getSelection();
                selection.removeAllRanges();
                selection.addRange(range);
                textArea.setSelectionRange(0, 999999);
            } else {
                textArea.select();
            }
        }

        function copyToClipboard() {
            document.execCommand('copy');
            document.body.removeChild(textArea);
        }

        copy = function (text) {
            createTextArea(text);
            selectText();
            copyToClipboard();
            alert('Link copiado com sucesso!');
        };

        return {
            copy: copy
        };
    })(window, document, navigator);

    $(document).ready(function () {
        $('body').on('click', '.load-more', function () {
            $('body').unbind('click', '.load-more');
            var td = $('table#cliente-geracao').find('tr:last').find('td:first');
            var nivel = td.data('nivel');
            if (nivel < 10) {
                var ids = td.data('ids');

                $.ajax({
                    url: window.root_path + "/ajax/getGeracao",
                    type: 'POST',
                    data: {ids: ids, nivel: nivel},
                    success: function (data) {
                        var returned = $.parseJSON(data);

                        if (returned.retorno == 'success') {
                            $('table#cliente-geracao > tbody').append(returned.html)
                        } else {
                            alert(returned.msg);
                        }

                        if (returned.load == 'true') {
                            $('body').bind('click', '.load-more');
                            $('body').find('.load-more i.fa-spinner').remove();

                        } else if (returned.load == 'false') {
                            $('div.div-load-more').remove();
                        }

                    }
                });
            }

        })

        getDependentes(<?php echo ClientePeer::getClienteLogado()->getId(); ?>,
            <?php echo ClientePeer::getClienteLogado()->getId(); ?>, '0');

        $('body').on('click', '.linkRede', function (e) {
            getDependentes($(this).data('idlogado'), $(this).data('id'), $(this).data('gen'));
            e.preventDefault();
        });

        $('body').on('click', '._btn-nome', function (e) {
            var cliente_nome_filtro = $("input._nome_cliente").val().toLowerCase();
            if (cliente_nome_filtro.length > 0) {
                $("table.table-rede-sized td.nome_cliente_filter").each(function (index) {
                    if ($(this).text().toLowerCase().indexOf(cliente_nome_filtro) >= 0) {
                        $(this).closest('tr').show();
                    } else {
                        $(this).closest('tr').hide();
                    }
                });

                $("ul.table-rede li.nome_cliente_filter").each(function (index) {
                    if ($(this).text().toLowerCase().indexOf(cliente_nome_filtro) >= 0) {
                        $(this).closest('ul').show();
                    } else {
                        $(this).closest('ul').hide();
                    }
                });

            } else {
                $("table.table-rede-sized td.nome_cliente_filter").each(function (index) {
                    $(this).closest('tr').show();
                });
                $("ul.table-rede li.nome_cliente_filter").each(function (index) {
                    $(this).closest('ul').show();
                });
            }
            e.preventDefault();
        });

        $('body').on('click', '._btn-nome-erase', function (e) {
            $("input._nome_cliente").val("");

            $("table.table-rede-sized td.nome_cliente_filter").each(function (index) {
                $(this).closest('tr').show();
            });
            $("ul.table-rede li.nome_cliente_filter").each(function (index) {
                $(this).closest('ul').show();
            });

            e.preventDefault();
        });

    });

    function getDependentes(idClienteLogado, idClienteRede, geracao) {
        $.ajax({
            url: "<?php echo $root_path; ?>/ajax/getClientesGeracao",
            data: {'idClienteLogado': idClienteLogado, 'idClienteRede': idClienteRede, 'geracao': geracao},
            type: "POST"
        }).done(function (html) {
            $('#redes-container').html(html);
        })
    }

    //$(function() {
    //    $(window).load(function() {
    //
    //        var planos = <?//= json_encode($planos) ?>//;
    //
    //        <?php //if(!empty($totalizadores['TOTAL'])):?>
    //            $.plot($("#site-statistics"),
    //                [{
    //                    data: <?php //echo json_encode($teste) ?>//,
    //                    // label: "Outubro"
    //                }],
    //                {
    //                    grid: {
    //                        labelMargin: 10,
    //                        hoverable: true,
    //                        borderWidth: 0
    //                    },
    //                    colors: ["#a6b0c2", "#71a5e7", "#aa73c2"],
    //                    xaxis: {
    //                        tickColor: "transparent",
    //                        ticks : <?php //echo json_encode($diasMesGraficoCarreira)?>//,
    //                        autoscaleMargin: 0,
    //                        font: {
    //                            color: '#8c8c8c',
    //                            size: 10
    //                        }
    //                    },
    //                    yaxis: {
    //                        min: 0,
    //                        max: planos ? planos.length - 1 : 0,
    //                        ticks: planos ? planos.length - 1 : 0,
    //                        font: {
    //                            color: '#8c8c8c',
    //                            size: 10
    //                        },
    //                        tickFormatter: function (val, axis) {
    //                            return planos[val];
    //                        }
    //                    },
    //                });
    //
    //            $("#site-statistics").bind("plothover", function (event, pos, item) {
    //                if (item) {
    //                    if (previousPoint != item.dataIndex) {
    //                        previousPoint = item.dataIndex;
    //                        $("#tooltip").remove();
    //
    //                        $('#infoProntos').show();
    //                        $('#infoProntos').text('Nível ' + item.datapoint[1] +   ' alcançado');
    //                        // showTooltip(item.pageX, item.pageY-7, parseFloat(item.datapoint[1]).formatMoney(2, ',', '.'));
    //                    }
    //                } else {
    //                    $('#infoProntos').hide();
    //                    $("#tooltip").remove();
    //                    previousPoint = null;
    //                }
    //            });
    //
    //            function showTooltip(x, y, contents) {
    //                console.log(x, y);
    //                $('<div id="tooltip" class="tooltip top in"><div class="tooltip-inner alert alert-primary">' + contents + '<\/div><\/div>').css({
    //                    display: 'none',
    //                    top: x,
    //                    left: y
    //                }).appendTo("body").fadeIn(200);
    //            }
    //
    //        <?php //else: ?>
    //        // $('#infoProntos').hide();
    //        // $("#site-statistics").html('<h1 class="text-center">Nada nesse período</h1>');
    //
    //            $.plot($("#site-statistics"),
    //                [{
    //                    data: <?php //echo json_encode($teste2) ?>//,
    //                    // label: "Outubro"
    //                }],
    //                {
    //                    grid: {
    //                        labelMargin: 10,
    //                        hoverable: true,
    //                        borderWidth: 0
    //                    },
    //                    colors: ["#a6b0c2", "#71a5e7", "#aa73c2"],
    //                    xaxis: {
    //                        tickColor: "transparent",
    //                        ticks : <?php //echo json_encode($diasMesGraficoCarreira)?>//,
    //                        autoscaleMargin: 0,
    //                        font: {
    //                            color: '#8c8c8c',
    //                            size: 10
    //                        }
    //                    },
    //                    yaxis: {
    //                        min: 0,
    //                        max: planos ? planos.length - 1 : 0,
    //                        ticks: planos ? planos.length - 1 : 0,
    //                        font: {
    //                            color: '#8c8c8c',
    //                            size: 10
    //                        },
    //                        tickFormatter: function (val, axis) {
    //                            return planos[val];
    //                        }
    //                    },
    //                });
    //
    //            $("#site-statistics").bind("plothover", function (event, pos, item) {
    //                if (item) {
    //                    if (previousPoint != item.dataIndex) {
    //                        previousPoint = item.dataIndex;
    //                        $("#tooltip").remove();
    //
    //                        $('#infoProntos').show();''
    //                        $('#infoProntos').text('Nível ' + item.datapoint[1] +   ' alcançado');
    //                        // showTooltip(item.pageX, item.pageY-7, parseFloat(item.datapoint[1]).formatMoney(2, ',', '.'));
    //                    }
    //                } else {
    //                    $('#infoProntos').hide();
    //                    $("#tooltip").remove();
    //                    previousPoint = null;
    //                }
    //            });
    //
    //        <?php //endif ?>
    //    });
    //});

</script>
<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>
</body>