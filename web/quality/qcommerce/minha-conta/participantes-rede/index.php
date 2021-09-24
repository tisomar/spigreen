<?php

use QPress\Template\Widget;

$strIncludesKey = 'minha-conta-participantes-rede';
include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";
require __DIR__ . '/actions/action.php';
?>

<body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-participantes-rede">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<style>
    #totalRede {
        font-weight: bold;
        font-size: 20px;
    }

    .icon_posicao_0{
        color: #F9A602;
        margin: 0;
        font-size: 18px;
        margin-right: 3px;
    }

    .icon_posicao_1{
        color: #C4CACE;
        margin: 0;
        font-size: 18px;
        margin-right: 3px;
    }

    .icon_posicao_2{
        color: #654321;
        margin: 0;
        font-size: 18px;
        margin-right: 3px;
    }
</style>
<main role="main">
    <?php
    Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Participantes rede' => '')));
    Widget::render('general/page-header', array('title' => 'Participantes rede'));
    Widget::render('components/flash-messages');
    ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-3">
                <?php Widget::render('general/menu-account', array('strIncludesKey' => $strIncludesKey)); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-9 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Total participantes da sua rede</h4>
                    </div>
                    
                    <div class="panel-body">   
                        <form action="<?php echo get_url_site() . '/minha-conta/participantes-rede/' ?>" role="form" method="get" class="form-disabled-on-load">
                            <?php Widget::render(
                                'forms/filtro-extratos',
                                array('dtInicio' => $dtInicio, 'dtFim' => $dtFim)
                            ); ?>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-theme btn-block">Filtrar</button>
                            </div>
                        </form>

                        <br>
                        <p> Existem <strong id="totalRede"><?= $totalRede ?> </strong>  pessoas fazendo parte da sua rede neste período. </p><br>
                      
                        <div class="row">
                            <div class="col-sm-4 col-xs-12">
                                <h3>Ranking maiores lideres</h3>

                                <?php foreach( $rankMaioresLideres as $key => $lideres ) : 
                                    $cliente = ClienteQuery::create()->filterById($lideres['CLIENTE_ID'])->findOne();

                                    $cidade = !empty($cliente->getEnderecoPrincipal()) ? $cliente->getEnderecoPrincipal()->getCidade()->getNome() : '';
                                    $estado = !empty($cliente->getEnderecoPrincipal()) ? $cliente->getEnderecoPrincipal()->getCidade()->getEstado()->getSigla() : '';
                                    $email = $cliente->getEmail();
                                    $coloracao =  "icon_posicao_" . $key;
                                    $iconPrimeiros = $key <= 2 ? "<span class='fa fa-trophy $coloracao'></span> " : $key + 1 .'° ';
                                    ?>
                                
                                    <p> <?= $iconPrimeiros . $lideres['NOME'] ?></p> 
                                    <p><strong id="totalRede"><?= $lideres['TOTAL_REDE'] ?> </strong> pessoas  </p>
                                    <p><?= $email ?>  </p>
                                    <p><?= $cidade .'/'.$estado?>  </p>
                                    <br>
                                    
                                <?php endforeach ?>
                            </div>
                           
                            <div class="col-sm-4 col-xs-12">
                                <h3>Ranking líderes em pontos recompra</h3>

                                <?php foreach( $rankLideresBonusRecompra as $key => $lideres ) : 
                                    $cliente = ClienteQuery::create()->filterById($lideres['CLIENTE_ID'])->findOne();

                                    $cidade = !empty($cliente->getEnderecoPrincipal()) ? $cliente->getEnderecoPrincipal()->getCidade()->getNome() : '';
                                    $estado = !empty($cliente->getEnderecoPrincipal()) ? $cliente->getEnderecoPrincipal()->getCidade()->getEstado()->getSigla() : '';
                                    $email = $cliente->getEmail();
                                    $coloracao =  "icon_posicao_" . $key;
                                    $iconPrimeiros = $key <= 2 ? "<span class='fa fa-trophy $coloracao'></span> " : $key + 1 .'° ';
                                    $pontos = format_number($lideres['TOTAL_BONUS_RECOMPRA'] ?? 0) 
                                    ?>

                                    <p> <?= $iconPrimeiros . $lideres['NOME'] ?></p> 
                                    <p><strong id="totalRede"><?= $pontos ?> </strong> pontos </p>
                                    <p><?= $email ?>  </p>
                                    <p><?= $cidade .'/'.$estado?>  </p>
                                    <br>
                                <?php endforeach ?>
                            </div>

                            <div class="col-sm-4 col-xs-12">
                                <h3>Ranking maiores recrutadores</h3>

                                <?php foreach( $rankLideresRecrutadores as $key => $lideres ) : 
                                    $cliente = ClienteQuery::create()->filterById($lideres['CLIENTE_ID'])->findOne();

                                    $cidade = !empty($cliente->getEnderecoPrincipal()) ? $cliente->getEnderecoPrincipal()->getCidade()->getNome() : '';
                                    $estado = !empty($cliente->getEnderecoPrincipal()) ? $cliente->getEnderecoPrincipal()->getCidade()->getEstado()->getSigla() : '';
                                    $email = $cliente->getEmail();
                                    $coloracao =  "icon_posicao_" . $key;
                                    $iconPrimeiros = $key <= 2 ? "<span class='fa fa-trophy $coloracao'></span> " : $key + 1 .'° ';
                                    $pontos = format_number($lideres['TOTAL_ADESAO'] ?? 0) 
                                    ?>

                                    <p> <?= $iconPrimeiros . $lideres['NOME'] ?></p> 
                                    <p><strong id="totalRede"><?= $pontos ?> </strong> pontos adesão </p>
                                    <p><?= $email ?>  </p>
                                    <p><?= $cidade .'/'.$estado?>  </p>
                                    <br>
                                <?php endforeach ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script type='text/javascript' src="<?= asset('/admin/assets/js/jquery-1.10.2.min.js')?>"></script>
<script>
   
</script>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>
</body>

</html>
