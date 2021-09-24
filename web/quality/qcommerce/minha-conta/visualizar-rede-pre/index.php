<?php
/* @var $objEnderecos Endereco */
require __DIR__ . '/actions/visualizar-rede.actions.php';
use QPress\Template\Widget;
$strIncludesKey = 'minha-conta-visualizar-rede';
include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";
?>

    <body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-visualizar-rede">
    <?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
    <?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
    <?php Widget::render('general/header'); ?>

    <main role="main">
        <?php
        Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Visualizar Rede' => '')));
        Widget::render('general/page-header', array('title' => 'Meus Endereços'));
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
                                Sua Rede.
                            </h3>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4>Configurações</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive no-label">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table">
                                            <thead>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Chave de indicação/Código de patrocinador:</td>
                                                    <td class="text-center">
                                                        <b><?php echo $clienteLogado->getChaveIndicacao(); ?></b>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Link de indicação:</td>
                                                    <td class="text-center">
                                                        <button class="btn btn-primary" id="link-patrocinador" onclick="copyToClipboard()" data-link="<?php echo get_url_site() . '/home/validPatrocinador?codigo_patrocinador=' . $clienteLogado->getChaveIndicacao(); ?>" >Clique para Copiar</button>

                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Total de participantes da rede:</td>
                                                    <td class="text-center">
                                                        <b><?php echo $clienteLogado->getTotalParticipantesRede() ?></b>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>Escolher o lado da rede automaticamente ao inserir novos cadastrados?</td>
                                                    <td>
                                                        <?php $on = (Cliente::LADO_AUTOMATICO === $clienteLogado->getLadoInsercaoCadastrados()) ? 'true' : 'false'  ?>
                                                        <div class="toggle toggle-dark" data-toggle-on="<?php echo $on ?>" id="toggle-rede-automatica"></div>
                                                    </td>
                                                </tr>
                                                <?php $display = (Cliente::LADO_AUTOMATICO === $clienteLogado->getLadoInsercaoCadastrados()) ? 'none' : 'table-row'  ?>
                                                <tr style="display: <?php echo $display ?>;">
                                                    <td>Inserir novos cadastrados a:</td>
                                                    <td>
                                                        <?php $on = (Cliente::LADO_DIREITO === $clienteLogado->getLadoInsercaoCadastrados()) ? 'false' : 'true'  ?>
                                                        <div class="toggle toggle-dark" data-toggle-on="<?php echo $on ?>" id="toggle-lado-rede"></div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                        
                    <div class="row">
                        <div class="col-xs-12">
                            <?php   ?>
                            <div style="display: none;">
                                <?php echo $htmlRede;  ?>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4>Visualização</h4>
                                </div>
                                <div class="panel-body" id="rede-container" style="overflow: hidden; overflow-x: auto;">
                                </div>
                            </div>
                            <div class="form-group">
                                <a href="<?php echo get_url_site(); ?>/minha-conta/visualizar-rede-pre/lightbox" target="_blank" data-lightbox="iframe" class="btn btn-theme btn-block" title="Visualização rede">Abrir em uma nova janela...</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>

    <?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>

    <script type="text/javascript">
        function copyToClipboard() {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($('#link-patrocinador').data('link')).select();
            document.execCommand("copy");
            $temp.remove();
            alert('Link copiado com sucesso!');
        }
    </script>
    </body>

</html>
