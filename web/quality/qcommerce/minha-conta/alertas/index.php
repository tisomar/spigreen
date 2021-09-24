<?php
use QPress\Template\Widget;
$strIncludesKey = 'minha-conta-alertas';
include QCOMMERCE_DIR . '/includes/security.php';
include QCOMMERCE_DIR . '/minha-conta/actions/alertas.actions.php';
include QCOMMERCE_DIR . '/includes/head.php';

?>

<body itemscope itemtype="http://schema.org/WebPage">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
    <?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
    <?php Widget::render('general/header'); ?>

    <main role="main">
        <?php
            Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Minha conta' => '/minha-conta/pedidos', 'Mensagens' => '')));
            Widget::render('general/page-header', array('title' => 'Mensagens'));
            Widget::render('components/flash-messages');
        ?>

        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-md-3">
                    <?php Widget::render('general/menu-account', array('strIncludesKey' => $strIncludesKey)); ?>
                </div>
                <div class="col-xs-12 col-md-9">
                    <?php if (count($documentsClientes) > 0) : ?>
                        <div class="table-vertical">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>Tipo da Mensagem</th>
                                        <th>Aceite</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <?php foreach ($documentsClientes as $key => $documentoClient) : /* @var $documentoClient DocumentoAlertaClientes */ ?>
                                    <tr class="<?php echo $documentoClient->getDataLido() == '' ? 'novo-mensagem' : '' ?>">
                                        <td><?php echo $documentoClient->getDocumentoAlerta()->getTitulo() ?></td>
                                        <td><?php echo $documentoClient->getDocumentoAlerta()->getTipoDesc() ?></td>
                                        <td><?php echo $documentoClient->getDataLido('d/m/Y H:i:s') ?></td>
                                        <td>
                                            <a class="visualizar-alerta" id="<?php echo $documentoClient->getId() ?>"
                                               href="javascript:void(0)">Visualizar mensagem</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>

                        <?php
                        Widget::render('components/pagination', array(
                            'pager' => $documentsClientes,
                            'href'  => get_url_site() . '/minha-conta/pedidos/',
                            'align' => 'center'
                        ));
                        ?>
                    <?php else : ?>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <span class="<?php icon('info'); ?>"></span> Nenhuma mensagem registrada!
                                    </div>
                                </div>
                            </div>
                            <br>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </main>
    <?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
    <?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>
</body>
</html>
