<?php

use QPress\Template\Widget;

$strIncludesKey = 'minha-conta-dados';
include QCOMMERCE_DIR . '/includes/security.php';
include QCOMMERCE_DIR . '/cadastro/actions/cadastro.actions.php';
include QCOMMERCE_DIR . '/includes/head.php';
?>

<body itemscope itemtype="http://schema.org/WebPage">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<?php $isPessoaJuridica = $objCliente->isPessoaJuridica();?>

<main role="main">
    <?php
    Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Minha conta' => '/minha-conta/pedidos', 'Dados Cadastrais' => '')));
    Widget::render('general/page-header', array('title' => 'Dados cadastrais'));
    Widget::render('components/flash-messages');
    ?>

    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-3">
                <?php Widget::render('general/menu-account', array('strIncludesKey' => $strIncludesKey)); ?>
            </div>
            <div class="col-xs-12 col-md-9">
                <h4>Utilize o formulário abaixo para alterar suas informações ou
                    <?php if($isPessoaJuridica) : ?>
                        <span id="pessoaFisica">
                            <a href="#">clique aqui para tornar-se ser Pessoa Física.</a>
                        </span>
                    <?php else: ?>
                        <span id="pessoaJuridica">
                            <a href="#">clique aqui para tornar-se Pessoa Jurídica.</a>
                        </span>
                    <?php endif ?>
                </h4>
                <br>
                <form role="form" method="post"
                      actions="<?php echo get_url_site() . '/cadastro/actions/cadastro.actions.php' ?>"
                      class="form-disabled-on-load" id="form-atualizar-cadastro">
                    <div class="row">
                        <!-- Pessoa Jurídica -->
                        <div id="mostraFormPJ" class="col-xs-12" <?php echo $isPessoaJuridica == false ? 'hidden' : ''?>>
                            <input type="hidden" id="pj" value="<?php echo $objCliente->getCnpj(); ?>">
                            <input type="hidden" id="removeCNPJ" name="removeCadastroPJ" value="0">
                            <?php
                            Widget::render('forms/dados-juridicos', array(
                                'objCliente' => $objCliente,
                                'strIncludesKey' => $strIncludesKey
                            ));
                            ?>
                        </div>
                        <div class="col-xs-12">
                            <h3>Dados Pessoa Física</h3>
                            <?php Widget::render('forms/dados-pessoais', array('objCliente' => $objCliente,)); ?>
                            <div class="form-group">
                                <button type="submit" class="btn btn-theme btn-block btn-atualizar">Atualizar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>

<script>
    $(document).ready(function(){
        $('#pessoaJuridica').click(function() {
            $('#mostraFormPJ').attr('hidden', false);
        })

        $('#pessoaFisica').click(function() {

            var optionsAjax = {
                title: 'Confirmação?',
                text: 'Você deseja realmente remover seu cadastro de pessoa jurídica?',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Sim",
                cancelButtonText: "Não"
            };

            swal(optionsAjax, function (isConfirm) {
                if (isConfirm) {
                    $('#removeCNPJ').val('1');
                    $('#mostraFormPJ').attr('hidden', true);
                }
            });
        })

        $('.btn-atualizar').on('click', function() {
            $('#form-atualizar-cadastro').submit();
        })
    });
</script>
</body>
</html>