<?php

/* @var $objEnderecos Endereco */
require __DIR__ . '/actions/visualizar-rede.actions.php';

use QPress\Template\Widget;

$strIncludesKey = 'minha-conta-visualizar-rede';
include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";
?>

<body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-visualizar-rede">
<?php
include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php';
include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php';
Widget::render('general/header');
?>

<div class="container-fluid" style="max-width: 1470px;">
    <?php
    Widget::render('components/breadcrumb', [
        'links' => [
            'Home' => '/home',
            'Minha Conta' => '',
            'Visualizar Rede' => ''
        ]
    ]);
    Widget::render('general/page-header', ['title' => 'Visualizar Rede']);
    Widget::render('components/flash-messages');
    ?>
    <style>
        .barra-progresso-plano-carreira {
            border-radius: 4px;
            border: 2px solid #4CAF50;
            height: 25px;
        }

        .barra-progresso-plano-carreira div {
            background-color: #4CAF50;
        }
    </style>
    <div class="row">
        <div class="col-xs-12">
            <div class="col-xs-12 col-md-3">
                <?php Widget::render('general/menu-account', ['strIncludesKey' => $strIncludesKey]); ?>
            </div>
            <div class="col-xs-12 col-md-9">
                <div class="row">
                    <div class="col-sm-8">
                        <h3>
                            Sua Rede
                        </h3>
                    </div>
                </div>

                <!-- FILTROS -->
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
                <!-- FIM DO FILTRO -->

                <!-- TOTALIZADOR DE PONTOS -->
                <div>&nbsp;</div>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <span class="<?php icon('info'); ?>"></span> Total de pontos
                        <strong><?php echo number_format($totalPontosPeriodo, 0, ',', '.')?></strong>.
<!--                        <strong>R$ 1000,00</strong>.-->
                    </div>
                </div>
                <!-- FIM TOTALIZADOR DE PONTOS -->

                <br>
                <?php if (/*$clienteLogado->getPlano() && $planoCarreiraAtual*/
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
                                                <h6><?= $planoCarreiraAtual->getPontos() ?> pontos</h6>
                                                <?= $planoCarreiraAtual->getGraduacao() ?>
                                                <img src="<?php echo asset('/admin/arquivos/' . $planoCarreiraAtual->getImagem()) ?>"
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
                            <div class="panel-heading">
                                <h4>Gerações</h4>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive no-label">
                                    <table class="table">
                                        <tbody>
                                        <tr>
                                            <td style="width: 80%; background-color: #f9f9f9;">
                                                <input
                                                        class="form-control _nome_cliente"
                                                        placeholder="Nome do Cliente"
                                                >
                                            </td>
                                            <td class="text-left" style="width: 10%; background-color: #f9f9f9;">
                                                <button class="btn btn-block btn-action _btn-nome ">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </td>
                                            <td class="text-left" style="width: 10%; background-color: #f9f9f9;">
                                                <button
                                                        class="btn btn-block btn-action _btn-nome-erase"
                                                        style="background-color: #cbcbcb;"
                                                >
                                                    <i class="fa fa-trash-o"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="redes-container"></div>
                            </div>
                        </div>
                    </div>
                </div>

<!--                <div class="row">-->
<!--                    <div class="col-xs-12">-->
<!--                        <div style="display: none;">-->
<!--                            --><?//= $htmlRede; ?>
<!--                        </div>-->
<!--                        <div class="panel panel-default">-->
<!--                            <div class="panel-heading">-->
<!--                                <h4>Visualização</h4>-->
<!--                            </div>-->
<!--                            <div class="panel-body" id="rede-container" style="overflow: hidden; overflow-x: auto;">-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="form-group">-->
<!--                            <a-->
<!--                                    href="--><?//= get_url_site() ?><!--/minha-conta/visualizar-rede/lightbox"-->
<!--                                    target="_blank"-->
<!--                                    data-lightbox="iframe"-->
<!--                                    class="btn btn-theme btn-block"-->
<!--                                    title="Visualização rede"-->
<!--                            >Abrir em uma nova janela...</a>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
            </div>
        </div>
    </div>
</div>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>

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
            <?php echo ClientePeer::getClienteLogado()->getId(); ?>, '0', <?= $mesSelecionado; ?>, <?= $anoSelecionado; ?>);

        $('body').on('click', '.linkRede', function (e) {
            getDependentes($(this).data('idlogado'), $(this).data('id'), $(this).data('gen'), $(this).data('mes'), $(this).data('ano'));
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

    function getDependentes(idClienteLogado, idClienteRede, geracao, mes = '', ano = '') {
        $.ajax({
            url: "<?php echo $root_path; ?>/ajax/getClientesGeracao",
            data: {
                'idClienteLogado': idClienteLogado,
                'idClienteRede': idClienteRede,
                'geracao': geracao,
                'mes': mes,
                'ano': ano
            },
            type: "POST"
        }).done(function (html) {
            $('#redes-container').html(html);
        })
    }
</script>
</body>
