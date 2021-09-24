<?php

include QCOMMERCE_DIR . '/admin/relatorio/helpers/js.php';

$showMenu = !isset($showMenu) ? true : $showMenu;
if ($showMenu) {
    include QCOMMERCE_DIR . '/admin/relatorio/helpers/menu.periodo.php';
}

?>

<div class="report-printable">
    <h1 class="print">
        Relatório de Faturamento
    </h1>
    <h4 class="print">
        Periodo:
        <?= $startDate->format('d/m/Y') . " à " . $endDate->format('d/m/Y')  ?>
    </h4>
</div>

<div class="col-xs-12">
    <div class="report-printable col-sm-12 col-lg-12">
        <h3>Valores de venda e faturamento</h3>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($totalizadoresVenda['valor_total_venda']) ?><br>
                <small class="text-muted">em venda</small>
            </h3>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($totalizadoresFaturamento['valor_total_faturamento']) ?><br>
                <small class="text-muted">em pagamentos confirmados</small>
            </h3>
        </div><br>
        <div class="clearfix"></div><br>

        <h3>Valores de bônus expansão</h3>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($bonusDireto) ?><br>
                <small class="text-muted">
                    bônus diretos
                </small>
            </h3>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($bonusIndireto) ?><br>
                <small class="text-muted">
                    bônus indiretos
                </small>
            </h3>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($totalBonusExpransao) ?><br>
                <small class="text-muted">
                    total bônus expansão ( bônus direto + bônus indireto)
                </small>
            </h3>
        </div><br>
        <div class="clearfix"></div><br>

        <h3>Valores de outros bônus</h3>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($bonusHotsite) ?><br>
                <small class="text-muted">
                    bônus hot site
                </small>
            </h3>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($bonusPreferencial) ?><br>
                <small class="text-muted">
                    bônus cliente preferêncial
                </small>
            </h3>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($bonusRecompra) ?><br>
                <small class="text-muted">
                    bônus recompra
                </small>
            </h3>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($bonusLiderenca) ?><br>
                <small class="text-muted">
                    bônus liderança
                </small>
            </h3>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($bonusAceleracao) ?><br>
                <small class="text-muted">
                    bônus aceleração
                </small>
            </h3>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($bonusDesempenho) ?><br>
                <small class="text-muted">
                    bônus desempenho
                </small>
            </h3>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($resgatePremiosEmDinheiro) ?><br>
                <small class="text-muted">
                    bônus pontos acumulados
                </small>
            </h3>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($fundoReservaPremiacao) ?><br>
                <small class="text-muted">
                    fundo de reserva para premiação
                </small>
            </h3>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($totalBonus) ?><br>
                <small class="text-muted">
                    total Bônus
                </small>
            </h3>
        </div>
        
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                <?= $percentSobreFaturamento ?> %<br>
                <small class="text-muted">
                    % sobre o faturamento
                </small>
            </h3>
        </div>
    </div>
</div>

<div class="clearfix"></div><br>

<div class="col-xs-12">
    <div class="report-printable col-sm-12 col-lg-12">
        <h3>Bônus frete</h3>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($totalizadoresVenda['valor_entrega']) ?> <br>
                <small class="text-muted">
                    total pagamento frete
                </small>
            </h3>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($custoProdutosLogistica) ?> <br>
                <small class="text-muted">
                    custo produto logistica
                </small>
            </h3>
        </div>
    </div>
</div>
<div class="clearfix"></div><br>


<div class="col-xs-12">
    <div class="report-printable col-sm-12 col-lg-12">
        <h3>Bônus produtos</h3>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($impostoSobreBonusProdutosMaster * $master) ?> <br>
                <small class="text-muted">
                    <?= $master ?> MASTER
                </small>
            </h3>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($impostoSobreBonusProdutosSupervisor * $supervisor) ?> <br>
                <small class="text-muted">
                    <?= $supervisor ?>  SUPERVISOR
                </small>
            </h3>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($impostoSobreBonusProdutosGerente * $gerente) ?> <br>
                <small class="text-muted">
                    <?= $gerente ?>  GERENTE
                </small>
            </h3>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($impostoSobreBonusProdutosExecutivo * $executivo) ?> <br>
                <small class="text-muted">
                    <?= $executivo ?>  EXECUTIVO
                </small>
            </h3>
        </div>

        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($total_bonus_produtos) ?> <br>
                <small class="text-muted">
                    Total bônus produtos
                </small>
            </h3>
        </div>
    </div>
</div>


<div class="clearfix"></div><br>


<div class="col-xs-12">
    <div class="report-printable col-sm-12 col-lg-12">
        <h3>Total bônus distribuídos</h3>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                R$ <?= format_money($total_bonus_distribuidos) ?> <br>
                <small class="text-muted">
                    total de bônus distribuídos
                </small>
            </h3>
        </div>
        <div class="col-xs-12 col-md-6 col-lg-3">
            <h3>
                <?= $perc_bonus_distribuidos ?> <br>
                <small class="text-muted">
                    % de bônus distribuídos
                </small>
            </h3>
        </div>
    </div>
</div>

<div class="col-xs-12">
    <h5></h5>
</div>

<div class="col-xs-12 visible-xs">
    <div class="report-printable">
        <div class="table-responsive ">
            <table class="table">
                <thead>
                <tr>
                    <th class="text-left">Período</th>
                    <th class="text-right">Valor</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($dataValorFaturamento as $i => $data) : ?>
                    <tr>
                        <td data-title="Período" class="text-left"><?= strip_tags($ticks[$i][1]); ?></td>
                        <td data-title="Valor" class="text-right">R$ <?= format_money($data[1]); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td data-title="" class="text-right"><b>Total:</b></td>
                    <td data-title="" class="text-right"><b>R$ <?= format_money($totalizadoresFaturamento['valor_total_faturamento']) ?></b></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script src="<?= asset('/admin/assets/plugins/charts-flot/jquery.flot.min.js') ?>"></script>
<script src="<?= asset('/admin/assets/plugins/charts-flot/jquery.flot.resize.min.js') ?>"></script>
<script src="<?= asset('/admin/assets/plugins/charts-flot/jquery.flot.orderBars.min.js') ?>"></script>

<script>
    $( document ).ready(function() {
        $('.btn-group-justified div a[href="?range=yesterday"').parent().remove();
        $('.btn-group-justified div a[href="?range=today"').parent().remove();
        $('.btn-group-justified div a[href="?range=last-week"').parent().remove();
    });
</script>