<div class="row">
    <div class="col-xs-12 visible-xs visible-sm">
        <?php require __DIR__ . '/includes/search.php';
        ?>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="row">
            <div class="col-md-6 div-cadagend-top">
                <div class="btn-agendamento-top">
                    <a href="javascript:;" class="btn btn-block btn-warning btn-icon icon-left btnAtividade btn-lg">
                        <i style="padding: 6px 6px;" class="entypo-newspaper"></i>
                        <span><?php echo escape(_trans('agenda.criar_novo_agendamento')); ?></span>
                    </a>

                </div>
            </div>
            <div class="col-md-6 div-cadagend-top">
                <div class="btn-cadastro-top">
                    <a href="javascript:void(0)"
                       class="btn btn-block btn-success btn-icon icon-left btn-lg adicionar-clientes">
                        <i style="padding: 6px 6px;" class="entypo-user-add"></i>
                        <span><?php echo escape(_trans('agenda.cadastro_cliente')); ?></span>
                    </a>
                </div>
            </div>
        </div>

    </div>
    <div class="col-sm-6">
        <div class="row">
            <form action="" method="POST" class="form-horizontal form-periodo pull-right col-xs-12">
                <div class="form-group">
                    <label class="col-sm-2 control-label hidden-xs"><?php echo escape(_trans('agenda.periodo')); ?>
                        :</label>
                    <label class="col-sm-4 control-label visible-xs"><?php echo escape(_trans('agenda.periodo_exibicao')); ?>
                        :</label>
                    <div class="col-sm-4">
                        <div class="input-group" id="rel-date-initial">
                            <span class="input-group-addon"><?php echo escape(_trans('agenda.de')); ?></span>
                            <input type="text" class="form-control datepicker" data-format="dd/mm/yyyy"
                                   name="dataInicial" value="<?php echo $dataInicial->format('d/m/Y'); ?>">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="input-group" id='rel-date-end'>
                            <span class="input-group-addon"><?php echo escape(_trans('agenda.ate')); ?></span>
                            <input type="text" class="form-control datepicker" data-format="dd/mm/yyyy" name="dataFinal"
                                   value="<?php echo $dataFinal->format('d/m/Y'); ?>">
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-default btn-icon icon-right pull-right">
                            <?php echo escape(_trans('agenda.filtrar')); ?>
                            <i class="fa fa-filter"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<hr>
<div class="row"><br></div>
<div class="row">
    <div class="col-sm-6 col-md-3">
        <a href="<?php echo $root_path ?>/distribuidores_novo/atividades/?dataInicial=<?php echo $dataInicial->format('d/m/Y'); ?>&dataFinal=<?php echo $dataFinal->format('d/m/Y'); ?>"
           class="tile-stats tile-red">
            <div class="icon"><i class="entypo-paper-plane"></i></div>
            <div class="num" data-start="0" data-end="<?php echo $valorEmAberto; ?>" data-prefix="R$ "
                 data-postfix=",00" data-duration="1500" data-delay="0">
                0
            </div>
            <h3><?php echo escape(_trans('agenda.valor_jogo')); ?></h3>
            <p><?php echo $atividadesAbertas . ' ' . escape(_trans('agenda.negociacoes_abertas')); ?></p>
            <p><?php echo escape(_trans('agenda.de')) . ' ' . $dataInicial->format('d/m/Y') . ' ' . mb_strtolower(_trans('agenda.ate'), 'UTF-8') . ' ' . $dataFinal->format('d/m/Y'); ?> </p>
        </a>
    </div>

    <div class="col-sm-6 col-md-3 ">
        <a href="<?php echo $root_path ?>/distribuidores_novo/atividades/?dataInicial=<?php echo $dataInicial->format('d/m/Y'); ?>&dataFinal=<?php echo $dataFinal->format('d/m/Y'); ?>&filter=finalizadas"
           class="tile-stats tile-green">
            <div class="icon"><i class="entypo-trophy"></i></div>
            <div class="num" data-start="0" data-end="<?php echo $valorFechado; ?>" data-prefix="R$ " data-postfix=",00"
                 data-duration="1500" data-delay="400">0
            </div>
            <h3><?php echo escape(_trans('agenda.valor_fechado')); ?></h3>
            <p><?php echo $atividadesFechadas . ' ' . escape(_trans('agenda.negociacoes_fechadas')); ?></p>
            <p><?php echo escape(_trans('agenda.de')) . ' ' . $dataInicial->format('d/m/Y') . ' ' . mb_strtolower(_trans('agenda.ate'), 'UTF-8') . ' ' . $dataFinal->format('d/m/Y'); ?> </p>
        </a>
    </div>

    <div class="clear visible-xs"></div>

    <div class="col-sm-6 col-md-3">

        <a href="<?php echo $root_path ?>/distribuidores_novo/atividades/?dataInicial=<?php echo $dataInicial->format('d/m/Y'); ?>&dataFinal=<?php echo $dataFinal->format('d/m/Y'); ?>&filter=agendamento_aberto"
           class="tile-stats tile-aqua">
            <div class="icon"><i class="entypo-suitcase"></i></div>
            <div class="num" data-start="0" data-end="<?php echo $atividadesAbertas; ?>" data-prefix=" " data-postfix=""
                 data-duration="1500" data-delay="600">0
            </div>
            <h3><?php echo escape(_trans('agenda.agendamentos_aberto')); ?></h3>
            <p><?php echo escape(_trans('agenda.de')) . ' ' . $dataInicial->format('d/m/Y') . ' ' . mb_strtolower(_trans('agenda.ate'), 'UTF-8') . ' ' . $dataFinal->format('d/m/Y'); ?> </p>
            <p>&nbsp;</p>
        </a>
    </div>

    <div class="col-sm-6 col-md-3">
        <a href="<?php echo $root_path ?>/distribuidores_novo/clientes/?filter[Comprou]=4" class="tile-stats tile-blue">
            <div class="icon"><i class="entypo-cc-nc"></i></div>
            <div class="num" data-start="0" data-end="<?php echo $clienteSemAtividade; ?>" data-postfix=""
                 data-duration="1500" data-delay="800">0
            </div>
            <h3><?php echo escape(_trans('agenda.clientes_sem_agendamentos')); ?></h3>
            <p><?php echo escape(_trans('agenda.total_geral')); ?></p>
            <p>&nbsp;</p>
        </a>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary panel-not-border panel-xs">
            <h3 class="cabecalho-table"><?php

            if ($atividadesPeriodo) {
                echo escape(_trans('agenda.agendamentos_periodo', array('%dataIni%' => $dataInicial->format('d/m/Y'), '%dataFin%' => $dataFinal->format('d/m/Y'))));
            } else {
                echo escape(_trans('agenda.agendamentos_dia'));
            }

            ?></h3>
            <table id="table-atividades" class="table large-only table-striped table-clientes" style="margin-top: 0;">
                <thead>
                <tr>
                    <th></th>
                    <th class="text-center"><?php echo escape(_trans('agenda.concluido')) ?></th>
                    <th><?php echo escape(_trans('agenda.tipo')) ?></th>
                    <th><?php echo escape(_trans('agenda.data')); ?></th>
                    <th><?php echo escape(_trans('agenda.cliente')); ?></th>
                    <th><?php echo escape(_trans('agenda.interesse')); ?></th>
                    <th><?php echo escape(_trans('agenda.telefone')); ?></th>
                </tr>
                </thead>
                <tbody><?php
                /* @var $evento DistribuidorEvento */
                if (count($eventosDia) > 0) {
                    foreach ($eventosDia as $evento) {
                        ?>
                        <tr<?php echo($evento->isAtrasado() ? ' class="tr-atrasada"' : ''); ?>
                        data-id="<?php echo $evento->getId(); ?>"
                        data-cliente-id="<?php echo $evento->getClienteDistribuidorId(); ?>"
                        data-valor="<?php echo $evento->getValor(); ?>"
                        data-finalizado="<?php echo(escape($evento->getStatus()) == 'FINALIZADO' ? 1 : 0); ?>"
                        data-category="<?php echo DistribuidorEventoPeer::getSubjectByText($evento->getAssunto())['category']; ?>"
                        style="cursor: pointer;">
                        <td>
                            <div class="visible-xs pull-right menudrop-icon" data-id="<?php echo $evento->getid(); ?>">
                                <a href="javascript:;" class="btnEditar">
                                    <i class="entypo-pencil" style="font-size: 1.2em"></i>
                                </a>
                            </div>
                        </td>
                        <td class="checkbox-item">
                            <div class="hidden-xs text-center">
                                <div class="checkbox checkbox-replace color-green" id="item-concluido">
                                    <input type="checkbox" class="check-itens atividadeCon"
                                           data-id="<?php echo $evento->getId(); ?>"
                                           data-cliente-id="<?php echo $evento->getClienteDistribuidorId(); ?>"
                                           data-valor="<?php echo $evento->getValor(); ?>">
                                </div>
                            </div>
                            <div class="visible-xs">
                                <div class="checkbox checkbox-replace color-green" id="item-concluido">
                                    <input type="checkbox" class="check-itens atividadeCon"
                                           data-id="<?php echo $evento->getId(); ?>"
                                           data-cliente-id="<?php echo $evento->getClienteDistribuidorId(); ?>"
                                           data-valor="<?php echo $evento->getValor(); ?>">
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php

                            if ($evento->getAssunto()) {
                                echo escape(_trans('agenda.' . $evento->getAssunto()));
                            }

                            ?>
                        </td>
                        <td class="data">
                            <?php echo escape($evento->getData('d/m/Y')); ?>
                        </td>
                        <td>
                            <?php echo escape($evento->getClienteDistribuidor()->getNomeCompleto()) ?>
                            <?php echo $evento->getClienteDistribuidor()->getLead() ? '<i class="indicacao entypo-star"></i>' : ''; ?>
                        </td>
                        <td><?php echo escape($evento->getInteresse()) ?></td>
                        <td><?php echo escape($evento->getClienteDistribuidor()->getTelefoneCelular()) ?></td>
                        </tr><?php
                    }
                } else {
                    ?>
                    <tr>
                    <td colspan="7"><?php echo escape(_trans('agenda.nenhum_agendamento')); ?></td>
                    </tr><?php
                }

                ?></tbody>
            </table>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-7">
        <div class="panel panel-primary panel-not-border panel-grafico" id="charts_env">
            <h3 class="cabecalho-table"><?php echo escape(_trans('agenda.evolucao_vendas')); ?></h3>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="line-chart">
                        <div id="chart3" class="morrischart" style="height: 300px"></div>
                    </div>
                </div>
            </div>
            <div class="panel-heading panel-footer">
                <div class="panel-title">
                    <ul class="list-unstyled list-inline">
                        <li class="">
                            <span style="background: #599DB4;"></span><?php echo escape(_trans('agenda.meta')); ?></li>
                        <li class="">
                            <span style="background: #074CA4;  "> </span><?php echo escape(_trans('agenda.venda')); ?>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <div class="col-sm-5">
        <div class="panel panel-primary panel-resumo-mensal">
            <h3 class="cabecalho-table"><?php echo escape(_trans('agenda.resumo_mensal')); ?></h3>
            <div class="panel-body">
                <div class="tab-content">
                    <table class="table table-striped" style="margin: 0;">
                        <tbody>
                        <tr>
                            <td><?php echo escape(_trans('agenda.pa_mensal')); ?></td>
                            <td><?php echo number_format(ClientePeer::getClienteLogado()->getTotalPontosMes(), 2, ',', '.'); ?></td>
                        </tr>
                        <!--                            <tr>-->
                        <!--                                <td>-->
                        <?php //echo escape(_trans('agenda.pa_rede_mensal')); ?><!--</td>-->
                        <!--                                <td>-->
                        <?php //echo number_format(ClientePeer::getClienteLogado()->getTotalPontosMesRedeFromCache()[0], 2, ',', '.'); ?><!--</td>-->
                        <!--                            </tr>-->
                        <tr>
                            <td><?php echo escape(_trans('agenda.pv_liberados')); ?></td>
                            <td><?php echo number_format(ClientePeer::getClienteLogado()->getSaldoPontos(), 2, ',', '.'); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo escape(_trans('agenda.pv_bloqueados')); ?></td>
                            <td><?php echo number_format(ClientePeer::getClienteLogado()->getSaldoPontosBloqueados(), 2, ',', '.'); ?></td>
                        </tr>
                        <!--                            <tr>-->
                        <!--                                <td>-->
                        <?php //echo escape(_trans('agenda.participantes')); ?><!--</td>-->
                        <!--                                <td>-->
                        <?php //echo ClientePeer::getClienteLogado()->getPontosRede() ? ClientePeer::getClienteLogado()->getPontosRede()->getTotalParticipantesRede() : 0; ?><!--</td>-->
                        <!--                            </tr>-->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
<!--        <div class="panel panel-primary panel-resumo-mensal">-->
<!--            <h3 class="cabecalho-table">--><?php //echo escape(_trans('agenda.meu_plano')); ?><!--</h3>-->
<!--            <div class="panel-body">-->
<!--                <div class="tab-content">-->
<!--                    <table class="table" style="margin: 0;">-->
<!--                        <tbody>-->
<!--                        <tr>-->
<!--                            <td>--><?php //echo escape(_trans('agenda.contatos')); ?><!--</td>-->
<!--                            <td>-->
<!--                                <div class="progress">--><?php
//
//                                    $widthContato = $utilizacaoContaMFW['max_contatos'] != 0 ? $utilizacaoContaMFW['total_contatos'] * 100 / $utilizacaoContaMFW['max_contatos'] : 0;
//
//                                    ?>
<!--                                    <div class="progress-bar progress-bar-info" role="progressbar"-->
<!--                                         aria-valuenow="--><?php //echo $utilizacaoContaMFW['total_contatos']; ?><!--"-->
<!--                                         aria-valuemin="0"-->
<!--                                         aria-valuemax="--><?php //echo $utilizacaoContaMFW['max_contatos']; ?><!--"-->
<!--                                         style="width: --><?php //echo number_format($widthContato, 2, ".", ""); ?><!--</div>-->
<!--/*                                </div>*/-->
<!--/*                                <small style="display: block; margin-top: -10px; padding-right: 3px; text-align: right;">*/--><?php ////echo number_format($utilizacaoContaMFW['total_contatos'], 0, "", ".") . ' / ' . number_format($utilizacaoContaMFW['max_contatos'], 0, "", "."); ?><!--<!--</small>-->
<!--                            </td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td>--><?php //echo escape(_trans('agenda.creditos_sms')); ?><!--</td>-->
<!--                            <td>-->
<!--                                <div class="progress">--><?php
//
//                                    $widthSMS = $utilizacaoContaMFW['max_sms'] != 0 ? $utilizacaoContaMFW['total_sms'] * 100 / $utilizacaoContaMFW['max_sms'] : 0;
//
//                                    ?>
<!--                                    <div class="progress-bar progress-bar-info" role="progressbar"-->
<!--                                         aria-valuenow="--><?php //echo $utilizacaoContaMFW['total_sms']; ?><!--"-->
<!--                                         aria-valuemin="0" aria-valuemax="--><?php //echo $utilizacaoContaMFW['max_sms']; ?><!--"-->
<!--                                         style="width: --><?php //echo number_format($widthSMS, 2, ".", ""); ?><!--</div>-->
<!--/*                                </div>*/-->
<!--/*                                <small style="display: block; margin-top: -10px; padding-right: 3px; text-align: right;">*/--><?php ////echo number_format($utilizacaoContaMFW['total_sms'], 0, "", ".") . ' / ' . number_format($utilizacaoContaMFW['max_sms'], 0, "", "."); ?><!--<!--</small>-->
<!--                            </td>-->
<!--                        </tr>-->
<!--                        <tr>-->
<!--                            <td colspan="2">-->
<!--                                <div class="hidden-xs">-->
<!--                                    <a href="--><?php //echo $root_path ?><!--/distribuidores_novo/credito-contato/"-->
<!--                                       class="btn btn-blue pull-right">-->
<!--                                        --><?php //echo escape(_trans('agenda.contrate_contato')); ?>
<!--                                    </a>-->
<!--                                    <a href="--><?php //echo $root_path; ?><!--/distribuidores_novo/credito-sms/"-->
<!--                                       class="btn btn-blue pull-right" style="margin-right: 2px;">-->
<!--                                        --><?php //echo escape(_trans('agenda.contrate_SMS')); ?>
<!--                                    </a>-->
<!--                                </div>-->
<!--                                <div class="visible-xs">-->
<!--                                    <a href="--><?php //echo $root_path ?><!--/distribuidores_novo/credito-contato/"-->
<!--                                       class="btn btn-blue col-xs-12">-->
<!--                                        --><?php //echo escape(_trans('agenda.contrate_contato')); ?>
<!--                                    </a>-->
<!--                                    <br>-->
<!--                                    <br>-->
<!--                                    <a href="--><?php //echo $root_path; ?><!--/distribuidores_novo/credito-sms/"-->
<!--                                       class="btn btn-blue col-xs-12">-->
<!--                                        --><?php //echo escape(_trans('agenda.contrate_SMS')); ?>
<!--                                    </a>-->
<!--                                </div>-->
<!--                            </td>-->
<!--                        </tr>-->
<!--                        </tbody>-->
<!--                    </table>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
    </div>
</div>

<nav id="bt-menu" class="bt-menu">
    <a href="#" class="bt-menu-trigger"><span><?php echo escape(_trans('agenda.menu')); ?></span></a>
    <ul>
        <li>
            <a href="javascript:;" onclick="jQuery('#enviar-email').modal('show');" class="bt-icon entypo-mail">
                <span><?php echo escape(_trans('agenda.enviar_email')); ?></span>
            </a>
        </li>
        <li>
            <a href="javascript:;" onclick="jQuery('#enviar-sms').modal('show');"
               class="bt-icon entypo-mobile">
                <span><?php echo escape(_trans('agenda.enviar_SMS')); ?></span>
            </a>
        </li>
        <li>
            <a href="javascript:;" onclick="jQuery('#modal-criar-atividade').modal('show');"
               class="bt-icon entypo-newspaper">
                <span><?php echo escape(_trans('agenda.criar_agendamento')); ?></span>
            </a>
        </li>
        <li><a href="javascript:void(0)" class="bt-icon entypo-user adicionar-clientes">
                <span><?php echo escape(_trans('agenda.novo_cliente')); ?></span>
            </a>
        </li>
    </ul>
</nav>

<?php


include __DIR__ . '/includes/modais.php';
include __DIR__ . '/../../atividades/views/includes/modais.php';
include __DIR__ . '/../../atividades/views/includes/modal_criar_atividade.php';

?>

<script src="<?php echo $root_path ?>/distribuidor_scripts/assets/js/libs/AnimatedBorderMenus/js/borderMenu.js"></script>

<script type="text/javascript">

    <?php

    if ($openModal/**&& !$video*/) {
        ?>$(window).load(function () {
        $('#modal-contatos-rede').modal('show');
    });<?php
    }

    ?>

    $(document).ready(function () {
        $('.adicionar-clientes').on('click', function () {
            $.post(
                "<?php echo $root_path . '/distribuidores_novo/home/actions/get.quantidade.clientes.actions.php' ?>",
                null,
                function (response) {
                        $('#modal-adicionar-clientes').modal('show');
                },
                'json'
            )
        });
//            $('#modal-contatos-rede').modal('show');
//        });
        var bar_chart = Morris.Bar({
            element: 'chart3',
            axes: true,
            data: [<?php

            for ($mes = 1; $mes <= 12; $mes++) {
                $ano = date('Y');
                $dtMes = new DateTime("$ano-$mes-01");

                $objMetaVenda = DistribuidorMetaVendaQuery::getMetaVendaDistribuidorNoMes($cliente, $dtMes);
                if ($objMetaVenda) {
                    $metaVenda = $objMetaVenda->getMeta();
                } else {
                    $metaVenda = DistribuidorConfiguracaoQuery::create()->getConfiguracaoDistribuidor($cliente)->getMetaVendasMensal();
                }

                $inicio = clone $dtMes;
                $inicio = $inicio->modify('first day of this month');
                $inicio->setTime(0, 0, 0);

                $fim = clone $dtMes;
                $fim = $fim->modify('last day of this month');
                $fim->setTime(23, 59, 59);

                $atividadesMes = DistribuidorEventoQuery::create()
                ->filterByCliente(ClientePeer::getClienteLogado())
                ->filterByStatus(DistribuidorEvento::STATUS_FINALIZADO)
                ->filterByData($inicio, Criteria::GREATER_EQUAL)
                ->filterByData($fim, Criteria::LESS_EQUAL)
                ->find();

                $vendasMes = 0;
                foreach ($atividadesMes as $at) {
                    $vendasMes += $at->getValor();
                }

                ?>{
                month: '<?php echo substr(_trans('meses.' . mb_strtolower(get_mes_extenso($mes), 'UTF-8')), 0, 3); ?>',
                venda: <?php echo $vendasMes; ?>,
                meta: <?php echo $metaVenda; ?>}<?php

                if ($mes < 12) {
                    ?>,<?php
                }
            }

            ?>],
            xkey: 'month',
            ykeys: ['meta', 'venda'],
            labels: ['<?php echo escape(_trans('agenda.meta')); ?>', '<?php echo escape(_trans('agenda.venda')); ?>'],
            barColors: ['#599DB4', '#074CA4'],
            xLabelAngle: 45,
            hideHover: 'auto',
            preUnits: "R$ "

        });


        // Seleciona modelo de sms
        $('#enviar-sms select[name="modeloSMS"]').on('change', function () {

            $('#enviar-sms textarea[name="sms[MENSAGEM]"]').attr('disabled', 'disabled');

            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/clientes/actions/busca_modelo.action.php?id=' + $(this).find('option:selected').val()
            }).done(function (result) {

                result = JSON.parse(result);

                $('#enviar-sms textarea[name="sms[MENSAGEM]"]').val(result);
                $('#enviar-sms textarea[name="sms[MENSAGEM]"]').removeAttr('disabled');
            });
        });

        // Seleciona modelo de email
        $('#enviar-email select[name="modeloEMAIL"]').on('change', function () {

            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/clientes/actions/busca_modelo.action.php?id=' + $(this).find('option:selected').val()
            }).done(function (result) {

                result = JSON.parse(result);

                $('#enviar-email iframe').contents().find('.wysihtml5-editor').html(result);
            });
        });

        $('.btnAtividade').on('click', function (e) {
            e.preventDefault();

            var id = $(this).data('id');

            $('#modal-criar-atividade select[name="evento[CLIENTE_DISTRIBUIDOR_ID]"]').val(id).trigger('change');
            $('#modal-criar-atividade').modal('show');
        });

        // Edita atividade
        $('#table-atividades tbody tr td:not(.checkbox-item), a.btnEditar').on('click', function () {

            var id = $(this).parent().data('id');

            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/atividades/actions/busca_atividade.action.php?id=' + id
            }).done(function (result) {

                result = JSON.parse(result);

                console.log(result);

                $('#modal-criar-atividade input[name="id"]').val(id);
                $('#modal-criar-atividade select[name="evento[CLIENTE_DISTRIBUIDOR_ID]"]').val(result.CLIENTE_ID).trigger('change');
                $('#modal-criar-atividade select[name="evento[ASSUNTO]"]').val(result.ASSUNTO).trigger('change');
                $('#modal-criar-atividade input[name="evento[INTERESSE]"]').val(result.INTERESSE);
                $('#modal-criar-atividade input[name="evento[DATA]"]').val(result.DATA);
                $('#modal-criar-atividade textarea[name="evento[DESCRICAO]"]').val(result.DESCRICAO);
                $('#modal-criar-atividade input[name="evento[VALOR]"]').val(result.VALOR);
                $('#modal-criar-atividade textarea[name="evento[DESCRICAO_SMS]"]').val(result.DESCRICAO_SMS);
                $('#modal-criar-atividade textarea[name="evento[DESCRICAO_EMAIL]"]').val(result.DESCRICAO_EMAIL);

                $('#modal-criar-atividade').modal('show');
            });

        });

        function startCountdown(cron, tempo) {

            // Se o tempo não for zerado
            if ((tempo - 1) >= 0) {

                var s = parseInt(tempo % 60);
                var m = parseInt((tempo / 60) % 60);
                var h = parseInt(tempo / 3600);

                if (h < 10) {
                    h = '0' + h;
                }

                if (m < 10) {
                    m = '0' + m;
                }

                if (s < 10) {
                    s = '0' + s;
                }

                horaImprimivel = h + ':' + m + ':' + s;

                cron.html(horaImprimivel);

                tempo--;

                setTimeout(function () {
                    startCountdown(cron, tempo);
                }, 1000);

            } else {
                // Quando o contador chegar a zero faz esta ação
            }
        }

        $('#modal-contatos-rede').on('show.bs.modal', function () {
            setTimeout(function () {
                $('.cronometro').each(function () {
                    startCountdown($(this), $(this).data('time'));
                });
            }, 1000);
        });

    });


</script>
