<?php

use QPress\Template\Widget;
use QPress\Frete\Services\RetiradaLoja\RetiradaLoja;
use \QPress\Frete\Services\Correios\Servicos\Correios04669;
use \QPress\Frete\Services\Correios\Servicos\Correios04162;
use \QPress\Frete\Services\Transportadora\FreteTransportadora;

$strIncludesKey = 'checkout-frete';

require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/actions/frete.actions.php';

$carrinho = $container->getCarrinhoProvider()->getCarrinho();

include_once __DIR__ . '/../includes/head.php';
?>

<body itemscope itemtype="http://schema.org/WebPage" data-page="checkout-pagamento">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.payment_process.tracking.php'; ?>
<?php include QCOMMERCE_DIR . '/includes/header-checkout.php'; ?>

<main role="main">
    <?php
    Widget::render('general/steps-checkout', array('active' => 2, 'progress' => '50'));
    Widget::render('general/page-header', array('title' => 'Opções de entrega'));
    Widget::render('components/flash-messages');
    Widget::render('carrinho/buttonContinuaCompra');
    ?>

    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-5">
                <?php
                $endereco = $carrinho->getEndereco();

                Widget::render('general/delivery-address', array(
                    'editable'          => true,
                    'address'           => $endereco,
                    'strIncludesKey'    => $strIncludesKey
                ));
                ?>
            </div>
            <form role="form" action="#" method="post" class="form-disabled-on-load">
                <div class="col-xs-12 col-md-7">
                    <div id="shipping-type">
                        <div class="panel panel-default">
                            <input type="text" name="centroDistribuicaoId" id="centro-distribuicao-id" value='1' hidden>
                            <input type="hidden" value="ABUYBLOCK" id="getTokenCompra" name="tokenConfirmacaoPermiteCompra" >
                            <?php
                            $hasDisponivel = false;

                            // Verifica se tem servicos de frete disponiveis
                            if (count($container->getFreteManager()->getModalidades()) > 0) :
                                $isMT = $endereco->getCidade()->getEstado()->getSigla() == 'MT';
                                ?>
                                <!-- <input type="hidden" id="pedido_retirada_loja" name="pedido_retirada_loja" value=""> -->
                                <ul class="list-group list-unstyled">
                                    <?php
                                    if ($carrinho->elegivelFreteGratis()) :
                                        $hasDisponivel = true;
                                        ?>
                                        <li class="list-group-item">
                                            <div class="radio">
                                                <label>
                                                        <span class="pull-left">
                                                            <input class="pull-left" type="radio" name="frete" required value='frete_gratis'>
                                                            <span class="name">
                                                                <strong>Frete Grátis</strong><br>
                                                                <small style="color: #d35400">O prazo para a entrega dentro do estado de MT leva em torno de 3 dias,<br> demais estados o prazo de entrega é de aproximadamente 10 dias.</small>
                                                            </span>
                                                        </span>
                                                    <span class="price pull-right">
                                                            <strong>R$ 0,00</strong>
                                                        </span>
                                                </label>
                                            </div>
                                        </li>
                                        <?php
                                        foreach ($container->getFreteManager()->getModalidades() as $modalidade) :
                                            if ($isMT && !in_array($modalidade->getNome(), ['frete_gratis', 'transportadora', 'retirada_loja'])) :
                                                continue;
                                            endif;

                                            if ($modalidade instanceof RetiradaLoja) :
                                                $hasDisponivel = true;
                                                ?>
                                                <li class="list-group-item">
                                                    <div class="radio">
                                                        <label>
                                                                <span class="pull-left">
                                                                    <input class="pull-left" type="radio" required name="frete" value="retirada_loja">
                                                                    <span class="name">
                                                                        <strong><?= $modalidade->getTitulo(); ?></strong>
                                                                    </span>
                                                                </span>
                                                        </label>
                                                    </div>
                                                </li>

                                                <div class="row box-select-retirada-loja" hidden>
                                                    <br>
                                                    <div class="col-xs-12">
                                                        <!-- Início combobox -->
                                                        <div class="col-xs-12">
                                                            <div class="form-group">
                                                                <select class="form-control" id="optionEstado">
                                                                    <option>Selecione o Estado...</option>
                                                                    <?php echo $optionEstados ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <!-- Início form-select retirada-na-loja -->
                                                        <div class="col-xs-12">
                                                            <div class="form-group">
                                                                <select class="form-control retirada_loja" id="retirada_loja" name="pedido_retirada_loja">
                                                                    <option value="">Selecione o Centro de Distribuição...</option>
                                                                </select>
                                                                <br>
                                                                <div class="lojaSelecionadaEndereco" hidden hidden>
                                                                    <span><p id='nome-loja' style="display: inline"></p></span><br>
                                                                    <span><p id='endereco-loja' style="display: inline"></p></span><br>
                                                                    <span><p id='telefone-loja' style="display: inline"></p></span><br>
                                                                    <span><p id='valor' style="display: inline"></p></span><br>
                                                                    <span><p id='prazo' style="display: inline"></p></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Fim form-select -->
                                                        <!-- Fim combobox -->
                                                    </div>
                                                </div>
                                            <?php
                                            endif;
                                        endforeach;
                                    else:
                                        foreach ($container->getFreteManager()->getModalidades() as $modalidade) :
                                            if ($isMT && !in_array($modalidade->getNome(), ['frete_gratis', 'transportadora', 'retirada_loja'])) :
                                                continue;
                                            endif;

                                            if ($modalidade instanceof RetiradaLoja) :
                                                $hasDisponivel = true;
                                                ?>
                                                <li class="list-group-item">
                                                    <div class="radio">
                                                        <label>
                                                                <span class="pull-left">
                                                                    <input class="pull-left" type="radio" required name="frete" value="retirada_loja">
                                                                    <span class="name">
                                                                        <strong><?= $modalidade->getTitulo(); ?></strong>
                                                                    </span>
                                                                </span>
                                                        </label>
                                                    </div>
                                                </li>

                                                <div class="row box-select-retirada-loja" hidden>
                                                    <br>
                                                    <div class="col-xs-12">
                                                        <!-- Início combobox -->
                                                        <div class="col-xs-12">
                                                            <div class="form-group">
                                                                <select class="form-control" id="optionEstado">
                                                                    <option>Selecione o Estado...</option>
                                                                    <?php echo $optionEstados ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <!-- Início form-select retirada-na-loja -->
                                                        <div class="col-xs-12">
                                                            <div class="form-group">
                                                                <select class="form-control retirada_loja" id="retirada_loja" name="pedido_retirada_loja">
                                                                    <option value="">Selecione o Centro de Distribuição...</option>
                                                                </select>
                                                                <br>
                                                                <div class="lojaSelecionadaEndereco" hidden>
                                                                    <span><p id='nome-loja' style="display: inline"></p></span><br>
                                                                    <span><p id='endereco-loja' style="display: inline"></p></span><br>
                                                                    <span><p id='telefone-loja' style="display: inline"></p></span><br>
                                                                    <span><p id='valor' style="display: inline"></p></span><br>
                                                                    <span><p id='prazo' style="display: inline"></p></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- Fim form-select -->
                                                        <!-- Fim combobox -->
                                                    </div>
                                                </div>

                                            <?php
                                            else :

                                                if($modalidade->getNome() == 'gollog'):
                                                    if(Config::get('has_gollog_frete') == false) continue;
                                                    if($isBlockTransporteGollog) continue;
                                                endif;

                                                if($modalidade->getNome() == 'tg'):
                                                    if(Config::get('has_tg_frete') == false) continue;
                                                    if($isBlockTransporteTG) continue;
                                                endif;

                                                $responseFrete = $modalidade->consultar($carrinho->generatePackage(null));

                                                if ($responseFrete->isDisponivel()) :
                                                    $hasDisponivel = true;
                                                    $freteSelecionado = $carrinho->getFrete();

                                                    if (!$responseFrete->hasErro()) :
                                                        $checked = $freteSelecionado == $modalidade->getNome() ? 'checked' : '';
                                                        ?>
                                                        <!-- INÍCIO PAC / SEDEX -->
                                                        <li class="list-group-item">
                                                            <div class="radio">
                                                                <label>
                                                                    <span class="pull-left">
                                                                        <input class="pull-left" type="radio" name="frete" value='<?php echo $modalidade->getNome(); ?>' required>
                                                                        <span class="name">
                                                                            <strong><?php echo $modalidade->getTitulo(); ?></strong>
                                                                        </span>
                                                                        <span class="deliverytime">
                                                                            (<?= $responseFrete->getPrazoExtenso() ?>)
                                                                        </span>
                                                                    </span>
                                                                    <span class="price pull-right">
                                                                        <strong>R$ <?php echo $responseFrete->getValor() ?></strong>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </li>
                                                        <!-- FIM PAC / SEDEX -->
                                                    <?php
                                                    else :
                                                        if (mb_stripos($responseFrete->getErro(), 'ERP-007') === false) :
                                                            ?>
                                                            <li class="list-group-item">
                                                                <div class="radio">
                                                                    <label>
                                                                                <span class="pull-left">
                                                                                    <input type="radio" disabled>
                                                                                    <span class="name">
                                                                                        <strong><?php echo $modalidade->getTitulo(); ?></strong>
                                                                                    </span>
                                                                                </span>
                                                                        <span class="pull-right">
                                                                                    <span class="error"><?php echo $responseFrete->getErro(); ?></span>
                                                                                </span>
                                                                    </label>
                                                                </div>
                                                            </li>
                                                        <?php
                                                        endif;
                                                    endif;
                                                endif;
                                            endif;
                                        endforeach;
                                    endif;
                                    ?>
                                </ul>
                            <?php
                            endif;
                            ?>
                        </div>

                        <?php if (!$hasDisponivel) : ?>
                            <div class="alert alert-danger">
                                Neste momento, não há meios de entrega disponíveis!
                            </div>
                        <?php else : ?>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 boxAvisoEstoque" <?php echo !empty($avisoEstoqueNegativo) ? '': 'hidden'?>>

                                    <div id="avisoEstoqueAction">
                                        <p style="color: #d35400">Alguns produtos de seu pedidos estão com estoque insuficiente no centro de distribuição responsável pelo seu endereço de entrega selecionado!</p>
                                        <?php foreach($avisoEstoqueNegativo as $key => $aviso) :?>
                                            <p><?= $aviso[0] ?></p>
                                        <?php endforeach ?>
                                    </div>

                                    <div id="avisoEstoqueAjax" hidden></div>

                                    <p style="color: #d35400" id="opcoesAviso">
                                        Opções atuais para finalizar a compra:
                                        <br>Substitua o produto que está com estoque insuficiente.
                                        <br>Escolha um endereço de entrega em outro estado.
                                        <br>Ou selecione outra loja para fazer a retirada!
                                    </p>
                                </div>
                                <div class="col-xs-12 col-sm-6 col-sm-offset-6">
                                    <div class="form-group">
                                        <button type="submit" <?= empty($avisoEstoqueNegativo) ? '' : ' disabled ' ?>class="btn btn-success btn-block btnPagamento">
                                            <i class="fa fa-lock"></i>
                                            Ir para o pagamento
                                            <i class="fa fa-angle-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>
<script>
    $(document).ready(function() {
        function checkStoque(centro_distribuicao_id) {
            var pathAjax = window.root_path + '/ajax/';

            $.ajax({
                url: pathAjax + 'ajax_consulta_estoque_retirada_loja/',
                data: { centro_distribuicao_id: centro_distribuicao_id, pedido_id: "<?= $carrinho->getId() ?>"},
                type: 'POST',
                success: function(data) {
                    var obj = JSON.parse(data);
                    if(obj[0].length > 0) {
                        var avisoContent = '<p style="color: #d35400"> Na loja selecionada verificamos que existem itens do seu pedido que estão com estoque insuficiente:</p>';
                        obj[0].map(aviso => {
                            avisoContent += `<p> ${aviso} </p>`;
                        })

                        $('.boxAvisoEstoque').attr('hidden', false);
                        $('.btnPagamento').attr('disabled', true);
                        $('#avisoEstoqueAction').attr('hidden', true);
                        $('#avisoEstoqueAjax').attr('hidden', false);
                        $('#avisoEstoqueAjax').html(avisoContent);
                        $('#getTokenCompra').val("ABUYBLOCK");
                    }else{
                        $('.boxAvisoEstoque').attr('hidden', true);
                        $('.btnPagamentoOriginal').attr('disabled', false);
                        $('.btnPagamento').attr('type', 'submit');
                        $('.btnPagamento').attr('disabled', false);
                        $('#getTokenCompra').val("ABUYOK");
                    }
                }
            })
        }

        $('.retirada_loja').change(function() {
            let estadoLoja = $(this).val();
            var pathAjax = window.root_path + '/ajax/';
            $.ajax({
                url: pathAjax + 'ajax_dados_da_loja/',
                data: {
                    estadoLoja: estadoLoja
                },
                type: 'POST',
                success: function(data) {
                    let obj = JSON.parse(data);
                    $('.lojaSelecionadaEndereco').attr('hidden', false);
                    $('#nome-loja').text(`Loja: ${obj.nome}`);
                    $('#telefone-loja').text(`Endereço: ${obj.telefone}`);
                    $('#endereco-loja').text(`Telefone: ${obj.endereco}`);
                    $('#valor').text(`Valor R$: ${obj.valor}`);
                    $('#prazo').text(`Prazo dia(s): ${obj.prazo}`);
                    $('#centro-distribuicao-id').val(obj.centroDistribuicaoId);

                    checkStoque(obj.centroDistribuicaoId);
                }
            });
        })


        $('#optionEstado').on('change', function() {
            let estadoId = $(this).val();

            $.ajax({
                url: window.root_path + '/ajax/ajax_option_estado_cidade/',
                data: {
                    estadoId: estadoId
                },
                type: 'POST',
                success: function(data) {
                    $('#retirada_loja').html(data);
                }
            });
        });

        $('.list-group-item input').change(function() {
            checkStoque("<?php echo $centroDistribuicaoId ?>")
        })

        setTimeout(function() {
            var freteSelecionado = $('[name="frete"]:input').val();

            $('[name="frete"][value="' + freteSelecionado + '"]:input')
                .click(function() {
                    $('#centro-distribuicao-id').val(3);
                })
                .change();
        }, 30);
    });
</script>

<?php include QCOMMERCE_DIR . '/includes/footer-checkout.php'; ?>
</body>

</html>
