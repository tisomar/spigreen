<div class="row">
    <div class="col-xs-12 visible-xs visible-sm">
        <?php include __DIR__ . '/includes/search.php'; ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <form action="" method="POST" class="form-horizontal form-periodo pull-right">
            <div class="form-group">
                <label class="col-sm-2 control-label hidden-xs"><?php echo escape(_trans('agenda.periodo')) ?>:</label>
                <label class="col-sm-4 control-label visible-xs"><?php echo escape(_trans('agenda.periodo_exibicao')) ?>:</label>
                <div class="col-sm-4">
                    <div class="input-group" id="rel-date-initial">
                        <span class="input-group-addon"><?php echo escape(_trans('agenda.de')) ?></span>
                        <input type="text" class="form-control datepicker" data-format="dd/mm/yyyy" name="dataInicial" value="<?php echo $dataInicial->format('d/m/Y'); ?>">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="input-group" id='rel-date-end'>
                        <span class="input-group-addon"><?php echo escape(_trans('agenda.ate')) ?></span>
                        <input type="text" class="form-control datepicker" data-format="dd/mm/yyyy" name="dataFinal" value="<?php echo $dataFinal->format('d/m/Y'); ?>">
                    </div>
                </div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-primary btn-icon icon-left pull-right">
                        <?php echo escape(_trans('agenda.filtrar')) ?>
                        <i class="glyphicon glyphicon-save-file"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<hr>

<div class="row">
    <div class="col-sm-4">
        <div class="panel panel-primary panel-not-border panel-grafico" id="charts_env">
            <h3 class="cabecalho-table"><?php echo escape(_trans('agenda.valores_ganhos')) ?></h3>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="qt-atividades">
                        <h3 class="text-success">R$ <?php echo number_format($valorGanho, 2, ',', '.'); ?>
                            <small class="small-valor"><?php echo $atividadesGanho; ?> <small><?php echo strtolower(escape(_trans('agenda.agendamentos'))); ?></small></small>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-primary panel-not-border panel-grafico" id="charts_env">
            <h3 class="cabecalho-table"><?php echo escape(_trans('agenda.valores_perdidos')) ?></h3>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="qt-atividades ">
                        <h3 class="text-danger">R$ <?php echo number_format($valorPerdido, 2, ',', '.'); ?>
                            <small class="small-valor"><?php echo $atividadesPerda; ?> <small><?php echo strtolower(escape(_trans('agenda.agendamentos'))); ?></small></small></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-primary panel-not-border panel-grafico" id="charts_env">
            <h3 class="cabecalho-table"><?php echo escape(_trans('agenda.valor_aberto')) ?></h3>
            <div class="panel-body">
                <div class="tab-content">
                    <div class="qt-atividades">
                        <h3 class="text-info ">R$ <?php echo number_format($valorTotal, 2, ',', '.'); ?>
                            <small class="small-valor"><?php echo $atividadesTotal; ?> <small><?php echo strtolower(escape(_trans('agenda.agendamentos'))); ?></small></small></h3>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-primary panel-not-border panel-grafico" id="charts_env">
            <h3 class="cabecalho-table"><?php echo escape(_trans('agenda.agendamentos_abertos')) ?></h3>
            <div class="panel-body">
                <div class="tab-content"><?php

                foreach ($assuntos as $assunto) {
//                        var_dump($assunto);die;

                    ?><div class="item">
                            <div class="titulo">
                                <span class="icone pull-left">
                                    <i class="<?php echo $assunto['icon'];?>"> </i>
                                </span> <strong><?php echo (isset($atividadesAbertas[$assunto['text']]) && $atividadesAbertas[$assunto['text']] != '' ? $atividadesAbertas[$assunto['text']] : 0); ?></strong>
                            <?php echo escape(_trans('agenda.' . $assunto['text'])) ?>
                            </div>
                            <div class="slider slider-blue" data-min="0" data-max="<?php echo $maiorAtividadeAberta; ?>" data-value="<?php echo isset($atividadesAbertas[$assunto['text']]) ? $atividadesAbertas[$assunto['text']] : 0; ?>"></div>
                        </div><?php
                }
                    
                ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-primary panel-not-border panel-grafico" id="charts_env">
            <h3 class="cabecalho-table"><?php echo escape(_trans('agenda.agendamentos_concluidos')) ?></h3>
            <div class="panel-body">
                <div class="tab-content"><?php
                
                foreach ($assuntos as $assunto) {
                    ?><div class="item">
                            <div class="titulo">
                                <span class="icone pull-left">
                                    <i class="<?php echo $assunto['icon']; ?>"> </i>
                                </span>
                                <strong><?php echo (isset($atividadesConcluidas[$assunto['text']]) && $atividadesConcluidas[$assunto['text']] != '' ? $atividadesConcluidas[$assunto['text']] : 0); ?></strong>
                            <?php echo escape(_trans('agenda.' . $assunto['text'])) ?>
                            </div>
                            <div class="slider slider-green" data-min="0" data-max="<?php echo $maiorAtividadeConcluida; ?>" data-value="<?php echo isset($atividadesConcluidas[$assunto['text']]) ? $atividadesConcluidas[$assunto['text']] : 0; ?>"></div>
                        </div><?php
                }
                    
                ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="panel panel-primary panel-not-border panel-grafico" id="charts_env">
            <h3 class="cabecalho-table"><?php echo escape(_trans('agenda.grafico')) ?></h3>
            <div class="panel-body">
                <div class="tab-pane active" id="line-chart">
                    <div id="chart3" class="morrischart" style="height: 300px"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-sm-4">
        <div class="panel panel-primary panel-not-border panel-grafico" id="charts_env">
            <h3 class="cabecalho-table"><?php echo escape(_trans('agenda.motivos_perdas')) ?></h3>
            <div class="panel-body">
                <div class="tab-content"><?php
                
                    $keys = array_keys($motivosPerda);
                
                foreach ($keys as $key) {
                    ?><div class="item">
                            <div class="titulo">
                                <strong><?php echo $motivosPerda[$key]; ?></strong>
                            <?php echo $key; ?>
                            </div>
                            <div class="slider slider-green" data-min="0" data-max="<?php echo $maiorMotivoPerda; ?>" data-value="<?php echo $motivosPerda[$key]; ?>"></div>
                        </div><?php
                }
                    
                ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-8">
        <div class="panel panel-primary panel-not-border panel-grafico" id="charts_env">
            <h3 class="cabecalho-table"><?php echo escape(_trans('agenda.produtos_vendidos')) ?></h3>
            <div class="panel-body">
                <div class="tab-content"><?php
                
                    /* @var $produto Produto */
                foreach ($produtos as $produto) {
                    ?><div class="item">
                            <div class="titulo">
                            <?php echo $produto->getNome(); ?>
                            </div>
                            <div class="slider slider-gold" data-min="0" data-max="<?php echo $maiorCompra; ?>" data-value="<?php echo $produto->getVirtualColumn('tem_compras'); ?>"></div>
                        </div><?php
                }
                    
                ?></div>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function () {

        var bar_chart = Morris.Bar({
            element: 'chart3',
            axes: true,
            data: [<?php
            
            for ($mes = 1; $mes <= 12; $mes++) {
                $dtMes = new DateTime("2017-$mes-01");

                $inicio = clone $dtMes;
                $inicio = $inicio->modify('first day of this month');
                $inicio->setTime(0, 0, 0);

                $fim = clone $dtMes;
                $fim = $fim->modify('last day of this month');
                $fim->setTime(23, 59, 59);

                $atividades = DistribuidorEventoQuery::create()
                    ->filterByCliente(ClientePeer::getClienteLogado())
                    ->filterByData($inicio, Criteria::GREATER_EQUAL)
                    ->filterByData($fim, Criteria::LESS_EQUAL)
                    ->filterByStatus(DistribuidorEvento::STATUS_FINALIZADO)
                    ->find();
                    
                $ganho = 0;
                $perdido = 0;
                    
                /* @var $atividade DistribuidorEvento */
                foreach ($atividades as $atividade) {
                    if ($atividade->getDistribuidorTemplateIdPerda()) {
                        $perdido += $atividade->getValor();
                    } else {
                        $ganho += $atividade->getValor();
                    }
                }
                    
                ?>{month: '<?php echo substr(_trans('meses.' . mb_strtolower(get_mes_extenso($mes), 'UTF-8')), 0, 3); ?>', ganho: <?php echo $ganho; ?>, perdido: <?php echo $perdido; ?>}<?php

if ($mes < 12) {
    ?>,<?php
}
            }
                
            ?>],
            xkey: 'month',
            ykeys: ['ganho', 'perdido'],
            labels: ['<?php echo escape(_trans('agenda.ganho')); ?>', '<?php echo escape(_trans('agenda.perdido')); ?>'],
            barColors: ['#599DB4', '#AC1818'],
            xLabelAngle: 45,
            hideHover: 'auto'
        });
        
    });

</script>
