<?php
require __DIR__ . '/actions/resgate.actions.php';
use QPress\Template\Widget;
$strIncludesKey = 'minha-conta-resgate-premios';
include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";
?>

    <body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-resgate-premios">
    <?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
    <?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
    <?php Widget::render('general/header'); ?>
    <style>
        table.borderless {
            border: none !important;
        }
        table.borderless td,table.borderless th{
            border: none !important;
        }
        .collapse-premio{
            display: flex;
            align-items:center;
        }

        .collapse-premio .chevron{
            position: absotute;
            right: 0;
        }

        .number{
            margin-left: 20px;
            color: #ec7000;
            background: #f5f5f5;
            text-align: center;
            -ms-flex-align: center;
            align-items: center;
            -ms-flex-pack: center;
            justify-content: center;
            display: -ms-flexbox;
            display: flex;
            width: 40px;
            border-radius: 50%;
            height: 40px;
            font-family: Itau Display Bold;
            font-size: 18px;
            line-height: 18px;
            -webkit-box-shadow: 0 0 0 1.4px #d2d2d2;
            box-shadow: 0 0 0 1.4px #d2d2d2;
        }

        .title h3{
            font-weight: bold;
        }
        .chevron{
            margin-right: 20px;
            margin-top: 15px;
        }

        .NotResgate {
            color: #d1cfcf
        }   
    </style>
    <main role="main">

        <input type="hidden" id="idCliente" value="<?php echo $clienteId?>">

        <?php
        Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Extrato Pontos' => '')));
        Widget::render('general/page-header', array('title' => 'Resgate'));
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
                                Solicitar Resgate Prêmios Acumulados.
                            </h3>
                        </div>
                    </div>
                    <br>
                
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <span class="<?php icon('info'); ?>"></span>

                                    Total de pontos acumulados disponíveis <strong><?php echo $pontosDisponiveis ?></strong>
                                    <p>Sua maior graduação: <?= $maxGraduacaoClienteDesc ?></p>
                                </div>
                            </div>       
                        </div>
                        <div class="col-xs-12">
                            <div class="panel panel-default" style="background-color: #efefef;">
                                <div class="panel-body">
                                    <h4>SOLICITAÇÃO DE RESGATE PRÊMIOS ACUMULADOS</h4>
                                    <p>Se escolher o resgate do prêmio em dinheiro, após confirmação da solicitação pela aquipe administrativa, você receberá um bônus direto no do prêmio que você selecionou! </p>
                                </div>
                            </div>
                        </div>
                        <!-- </?php if ($bloqueiaResgate): ?>
                            <div class="col-xs-12 text-center">
                                Realize a sua ativação mensal para efetuar resgates.
                            </div>
                        </?php elseif ($pontosDisponiveis >= $pontosMinimo) : ?> -->

                        <div class="col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    
                                    <h3>Prêmio Pontos Acumulados</h3>
                                    <p>Acumule pontos e ganhe prêmios e viagens incríveis</p>

                                    <?php if($pontosReservados == 0) :?>
                                        <form role="form" method="post" class="form-disabled-on-load formBancoResgate">
                                            <div class="form-group">
                                                <input type="hidden" id="premio" required name="resgate[PREMIO]">
                                                <input type="hidden" id="pontos" required name="resgate[PONTOS]">
                                            </div>
                                        </form>   

                                        <?php
                                        $ultimoPremioResgatado = ResgatePremiosAcumuladosQuery::create()
                                            ->filterByClienteId($cliente->getId())
                                            ->filterBySituacao([ResgatePremiosAcumulados::SITUACAO_PENDENTE, ResgatePremiosAcumulados::SITUACAO_EFETUADO])
                                            ->orderByPontosResgate(Criteria::DESC)
                                            ->findOne();

                                        foreach($premiosList as $key => $premio) :

                                            $pontosVme = $gerenciador->getPontosVME($cliente, $premio->getPontosResgate());

                                            if( $premio->getGraduacaoMinimaId() !== null) :
                                                $graduacaoMinima = PlanoCarreiraQuery::create()
                                                ->filterByID($premio->getGraduacaoMinimaId(), Criteria::EQUAL)
                                                ->findOne()->getGraduacao();
                                            else:
                                                $graduacaoMinima = ' **** ';
                                            endif;
                                            
                                            $condicao = 
                                                $pontosDisponiveis >= $premio->getPontosResgate() &&
                                                $premio->getGraduacaoMinimaId() <= $maxGraduacaoCliente &&
                                                $pontosVme >= $premio->getPontosResgate() &&
                                                (is_null($ultimoPremioResgatado) || $premio->getPontosResgate() > $ultimoPremioResgatado->getPontosResgate());
                                            $collapse = $condicao ? 'collapse' : '';
                                            $NotResgate = !$condicao ? 'NotResgate' : '';
                                            ?>
                                            <div class="panel-group" >
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <div class="row collapse-premio" data-toggle="<?= $collapse ?>" href="#collapse<?= $key?>" >
                                                            <div class="number col-sm-10"><?= $key + 1?></div>
                                                            <div class="col-sm-6 title">
                                                                <h3 class="<?= $NotResgate ?>"> 
                                                                    Pontos acumulados: <?php echo $premio->getPontosResgate() . 
                                                                    '<br>VME: ' . $pontosVme . 
                                                                    '<br>Graduação mínima: ' . $graduacaoMinima
                                                                    ?>
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="collapse<?= $key?>" class="panel-collapse collapse box-premios">
                                                        <div class="panel-body">
                                                            <p>1° Opção de resgate:
                                                                <a 
                                                                    href="#void" id="primeiroPremio_<?= $key?>" 
                                                                    data-premio="<?= $premio->getPrimeiroPremio() ?>"
                                                                    data-pontos="<?= $premio->getPontosResgate()?>"
                                                                >
                                                                    <?= $premio->getPrimeiroPremio() ?>
                                                                </a>
                                                            </p>

                                                            <?php if( $premio->getSegundoPremio() != '') : ?>
                                                            <p>2° Opção de resgate: 
                                                                <a 
                                                                    href="#void" 
                                                                    id="segundoPremio_<?= $key?>" 
                                                                    data-premio="<?= $premio->getSegundoPremio() ?>"
                                                                    data-pontos="<?= $premio->getPontosResgate()?>"
                                                                >
                                                                    <?= $premio->getSegundoPremio() ?>
                                                                </a>
                                                            </p>
                                                            <?php endif?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach ?>
                                    <?php else: ?>
                                        <p>No momento existe uma solicitação de resgate pendente, aguarte até que esta solicitação seja concluída</p>
                                    <?php endif ?>
                                </div>    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script type="text/javascript">
        $(document).ready(function () {

            $('.resgatar').click(function() {
                $('#idBancoResgate').val($(this).val())
                if($('#qtd-pontos').val() == '') {
                    alert('Imforme o valor de pontos a ser resgatados');
                }else{
                    let resposta = confirm('Confirma o resgate através desta conta?');
                    if(resposta == true) {
                        $('.formBancoResgate').submit();
                    }else{
                        return false;
                    }
                }
            });

            $('.box-premios a').on('click', function() {
                const premio = $(this).data('premio');
                const pontos = $(this).data('pontos');

                $('#premio').val(premio);
                $('#pontos').val(pontos);

                var optionsAjax = {
                title: 'Confirmação?',
                text: `Você deseja realmente solicitar o resgate de ${premio}, usando ${pontos} de seus pontos acumulados?`,
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Sim",
                cancelButtonText: "Não"
                };

                swal(optionsAjax, function (isConfirm) {
                    if (isConfirm) {
                        $('.formBancoResgate').submit();
                    }
                });
            })
        });
    </script>

    <?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>

    <?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>
    </body>

</html>
