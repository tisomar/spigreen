<?php

use QPress\Template\Widget;

$strIncludesKey = 'minha-conta-participantes-inativos';
include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";
require __DIR__ . '/actions/action.php';
?>

<body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-participantes-inativos">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<style>
    #totalRede {
        font-weight: bold;
        font-size: 20px;
    }
</style>
<main role="main">
    <?php
    Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Participantes inativos' => '')));
    Widget::render('general/page-header', array('title' => 'Participantes inativos'));
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
                        <h4>Participantes de sua rede inativos</h4>
                    </div>
                    
                    <div class="panel-body">   
                        <form action="<?php echo get_url_site() . '/minha-conta/participantes-inativos/' ?>" role="form" method="POST" class="form-disabled-on-load">
                            <div class="form-group">
                                <input type="text" class="form-control" name='cliente' placeholder="Cliente"><br>
                                <button type="submit" class="btn btn-theme btn-block">Filtrar</button>
                            </div>
                        </form>

                        <br>
                        <p> Existem <strong id="totalRede"><?= count($clientesInativos) ?> </strong>  pessoas de sua rede inativos neste mês. </p><br>

                        <?php if( count($clientesInativos) > 0) :?> 
                            <div class="table-vertical">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th class="text-center" nowrap>Nome</th>
                                        <th class="text-center">E-mail</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php foreach( $clientesInativos as $key => $cliente ) : 
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo $cliente->getNomeCompleto() ?></td>
                                            <td class="text-center"><?php echo escape($cliente->getEmail()) ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else : ?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <span class="<?php icon('info'); ?>"></span> Nenhum cliente inativo encontrado.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
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
