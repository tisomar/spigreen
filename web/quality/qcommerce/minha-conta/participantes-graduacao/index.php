<?php

use QPress\Template\Widget;

$strIncludesKey = 'minha-conta-participantes-graduacao';
include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";
require __DIR__ . '/actions/action.php';
?>

<body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-participantes-graduacao">
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
    Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Participantes graduação' => '')));
    Widget::render('general/page-header', array('title' => 'Participantes graduação'));
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
                        <h4>Participantes de sua rede</h4>
                    </div>
                    
                    <div class="panel-body">   
                        <form action="<?php echo get_url_site() . '/minha-conta/participantes-graduacao/' ?>" role="form" method="POST" class="form-disabled-on-load">
                            <div class="form-group">
                                <input type="text" class="form-control" name='cliente' placeholder="Cliente"><br>
                                <button type="submit" class="btn btn-theme btn-block">Filtrar</button>
                            </div>
                        </form>

                        <br>

                        <?php if( count($clienteRedeGraduacao) > 0) :?> 
                            <div class="table-vertical">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th class="text-center"nowrap>Nome</th>
                                        <th class="text-center">Telefone</th>
                                        <th class="text-center">E-mail</th>
                                        <th class="text-center">Graduação</th>
                                        <th class="text-center">Maior graduação</th>
                                        <th class="text-center">Pontos</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php foreach( $clienteRedeData as $key => $cliente ) : 
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo $cliente['Nome'] ?></td>
                                            <td class="text-center"><?php echo escape($cliente['Telefone']) ?></td>
                                            <td class="text-center"><?php echo escape($cliente['Email']) ?></td>
                                            <td class="text-center">
                                                <?php if ($cliente['GraduacaoPathIcon'] !== null): ?>
                                                    <img src="<?php echo asset('/admin/arquivos/' . $cliente['GraduacaoPathIcon']) ?>"
                                                            width='50vmin'
                                                            class="card-img-top pull-center" alt="...">
                                                <?php endif ?>
                                                <div class="card-body">
                                                    <h5 class="card-title">
                                                        <strong><?php echo $cliente['Graduacao'] ?></strong>
                                                    </h5>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($cliente['MaiorGraduacaoPathIcon'] !== null): ?>
                                                    <img src="<?php echo asset('/admin/arquivos/' . $cliente['MaiorGraduacaoPathIcon']) ?>"
                                                            width='50vmin'
                                                            class="card-img-top pull-center" alt="...">
                                                <?php endif ?>
                                                <div class="card-body">
                                                    <h5 class="card-title">
                                                        <strong><?php echo $cliente['MaiorGraduacao'] ?></strong>
                                                    </h5>
                                                </div>
                                            </td>
                                            <td class="text-center"><?php echo escape($cliente['pontos']) ?></td>
                                        </tr>


                                    <?php endforeach ?>
                                    </tbody>
                                </table>

                                <div class="align-center">
                                    <p>
                                        Resultado(s) <?= $activePage ?> - <?= floor($pagigas) ?> de <?= $countResults ?>                                 
                                    </p>

                                    <ul class="pagination">
                                        <li><a href="http://localhost/minha-conta/participantes-graduacao?page=1" title="Primeira página"><span class="fa fa-angle-double-left"></span></a></li>
                                        <li><a href="http://localhost/minha-conta/participantes-graduacao?page=1" title="Página anterior" class="fa fa-angle-left"></a></li>
                                        <?php if($PrevPage !== $activePage) : ?>
                                            <li class=""><a href="http://localhost/minha-conta/participantes-graduacao?page=<?= $PrevPage ?>"> <?= $PrevPage?> </a></li>
                                        <?php endif ?>
                                        <li class="active"><a href="http://localhost/minha-conta/participantes-graduacao?page=<?= $activePage ?>"> <?= $activePage ?> </a></li>
                                        <?php if( $nextPage <= floor($pagigas) ): ?>
                                            <li class=""><a href="http://localhost/minha-conta/participantes-graduacao?page=<?= $nextPage ?>"> <?= $nextPage ?> </a></li>
                                        <?php endif ?>
                                        <?php if( $nextPage + 1 <= floor($pagigas) ): ?>
                                            <li><a href="http://localhost/minha-conta/participantes-graduacao?page=<?= $nextPage + 1?>" title="Próxima página" class="fa fa-angle-right"></a></li>
                                            <li><a href="http://localhost/minha-conta/participantes-graduacao?page=<?= floor($pagigas)?>" title="Última página" class="fa fa-angle-double-right"></a></li>
                                        <?php endif ?>
                                    </ul>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <span class="<?php icon('info'); ?>"></span> Nenhum cliente com este nome encontrado.
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
