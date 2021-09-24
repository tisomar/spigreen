<?php

use QPress\Template\Widget;

$strIncludesKey = 'cadastro';
include('actions/cadastro.actions.php');
include(QCOMMERCE_DIR . "/includes/head.php");
?>
<body itemscope itemtype="http://schema.org/WebPage" data-page="cadastro">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<main role="main">

    <?php
    Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Cadastro' => '')));
    Widget::render('general/page-header', array('title' => 'Cadastro'));
    Widget::render('components/flash-messages');

    if ($taxaCadastro) :
        ?>
        <div class="container">
            <form role="form" method="post" class="form-disabled-on-load form-cadastro">
                <div id="cadastro">
                    <input type="hidden" name="redirecionar"
                           value="<?php echo $request->request->get('redirecionar'); ?>">
                    <div class="row">

                        <?php if (Config::get('clientes.tipo_cadastro')
                            == Config::CLIENTES_TIPO_CADASTRO_AMBOS) : ?>
                            <div class="col-xs-12 col-md-6 col-md-offset-3">
                                <?php Widget::render('forms/tipo-de-pessoa'); ?>
                                <hr>
                            </div>

                        <?php endif; ?>

                        <?php if (Config::get('clientes.tipo_cadastro') != Config::CLIENTES_TIPO_CADASTRO_PJ) : ?>
                            <?php if (Config::get('clientes.tipo_cadastro')
                                != Config::CLIENTES_TIPO_CADASTRO_PF) : ?>
                                <div id="company-data" class="collapse col-xs-12 col-md-6">
                                    <?php Widget::render(
                                        'forms/dados-juridicos',
                                        array('objCliente' => $objCliente)
                                    ); ?>
                                </div>
                            <?php endif; ?>
                        <?php else : ?>
                            <div id="company-data" class="col-xs-12 col-md-6">
                                <?php Widget::render('forms/dados-juridicos', array('objCliente' => $objCliente)); ?>
                            </div>
                        <?php endif; ?>

                        <div id="person-data" class="col-xs-12 col-md-6 <?= Config::get('clientes.tipo_cadastro') != Config::CLIENTES_TIPO_CADASTRO_PJ ? 'col-md-offset-3' : ''?>">
                            <?php Widget::render('forms/dados-pessoais', array('objCliente' => $objCliente)); ?>
                            <div class="form-group">
                                <?php Widget::render('forms/termos-uso'); ?>
                            </div>
                            <div class="form-group">
                                <?php Widget::render('forms/termo-politica-privacidade'); ?>
                            </div>
                            <div class="form-group">
                                <?php Widget::render('forms/termo-plano-compensacao'); ?>
                            </div>
                            <div class="form-group">
                                <?php Widget::render('forms/receber-newsletter'); ?>
                            </div>
                            <div class="form-group">
                                <a id="pagamento-cadastro" class="btn btn-theme btn-block">Proximo Passo</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="endereco" style="display: none">
                    <div class="row">

                        <div class="col-xs-12 col-md-6 col-md-offset-3">
                            <div class="form-group">
                                <label for="address-identification">Identificação do endereço:</label>
                                <input type="text" class="form-control" id="address-identification"
                                       name="e[IDENTIFICACAO]" value="<?php echo $objEndereco->getIdentificacao(); ?>"
                                       placeholder="Ex: Minha Casa, Meu Trabalho, Casa da praia">
                            </div>

                            <div class="form-group">
                                <label for="address-name">Nome do Destinatário:</label>
                                <input type="text" class="form-control" id="address-name" name="e[NOME_DESTINATARIO]"
                                       value="<?php echo $objEndereco->getNomeDestinatario() ?>">
                            </div>

                            <?php Widget::render('forms/dados-endereco', array(
                                'objEndereco' => $objEndereco
                            )); ?>

                            <div class="form-group">
                                <div class="col-xs-12 col-md-6"
                                     style="padding-left: 0px;padding-right: 5px;margin-bottom: 15px">
                                    <a id="editar-cadastro" class="btn btn-primary btn-block">Passo Anterior</a>
                                </div>
                                <div class="col-xs-12 col-md-6" style="padding-right: 0px;padding-left: 5px;">
                                    <button type="submit" class="btn btn-theme btn-block">Finalizar Cadastro</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    <?php else : ?>
        <div class="container">
            <form role="form" method="post" class="form-disabled-on-load form-cadastro">
                <input type="hidden" name="redirecionar" value="<?php echo $request->request->get('redirecionar'); ?>">

                <div class="row">

                    <?php if (Config::get('clientes.tipo_cadastro') == Config::CLIENTES_TIPO_CADASTRO_AMBOS) : ?>
                        <div class="col-xs-12 col-md-6 col-md-offset-3">
                            <?php Widget::render('forms/tipo-de-pessoa'); ?>
                            <hr>
                        </div>

                    <?php endif; ?>

                    <?php if (Config::get('clientes.tipo_cadastro') != Config::CLIENTES_TIPO_CADASTRO_PJ) : ?>
                        <?php if (Config::get('clientes.tipo_cadastro') != Config::CLIENTES_TIPO_CADASTRO_PF) : ?>
                            <div id="company-data" class="collapse col-xs-12 col-md-6">
                                <?php Widget::render('forms/dados-juridicos', array('objCliente' => $objCliente)); ?>
                            </div>
                        <?php endif; ?>
                    <?php else : ?>
                        <div id="company-data" class="col-xs-12 col-md-6">
                            <?php Widget::render('forms/dados-juridicos', array('objCliente' => $objCliente)); ?>
                        </div>
                    <?php endif; ?>

                    <div id="person-data" class="col-xs-12 col-md-6 <?= Config::get('clientes.tipo_cadastro') != Config::CLIENTES_TIPO_CADASTRO_PJ ? 'col-md-offset-3' : ''?>">
                        <?php Widget::render('forms/dados-pessoais', array('objCliente' => $objCliente)); ?>

                        <div class="form-group">
                            <?php Widget::render('forms/termos-uso'); ?>
                        </div>
                        <div class="form-group">
                            <?php Widget::render('forms/termo-politica-privacidade'); ?>
                        </div>
                        <div class="form-group">
                            <?php Widget::render('forms/termo-plano-compensacao'); ?>
                        </div>
                        <div class="form-group">
                            <?php Widget::render('forms/receber-newsletter'); ?>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-theme btn-block">Finalizar cadastro</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    <?php endif; ?>
</main>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<?php if (!ClientePeer::isAuthenticad() && $ativacaoPatrocinador) : ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.form-cadastro').on('submit', function (e) {
                var valorNome = $('#register-name').val();
                var valorTelefone = $('#register-phone').val();
                var valorCPF = $('#register-cpf').val();
                var valorDataNascimento = $('#birth-date').val();
                var valorEmail = $('#register-email').val();
                var valorEmailConfirmacao = $('#register-email-confirmation').val();

                var form = this
                var $submitBtn = $(form).find('button[type="submit"]');

                if (
                    $('#register-email-confirmation').size() > 0 &&
                    valorEmail.trim() !== valorEmailConfirmacao.trim()
                ) {
                    swal({
                        title: 'Formulário inválido!',
                        text: 'Os e-mails não batem.'
                    })
                    $submitBtn.prop('disabled', false);
                    removeLoaderFromElement($submitBtn);

                    return false;
                }

                var patrocinadorDesc = $('#patrocinador-nome').val();
                var patrocinadorId = $('#patrocinador-id').val();

                if (patrocinadorId) {
                    var options = {
                        title: 'Confirmação Patrocinador',
                        text: 'Confirma ' + patrocinadorDesc + ' como seu patrocinador? Ele não poderá ser alterado depois de confirmado.',
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Sim",
                        cancelButtonText: "Não"
                    };

                    swal(options, function (isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                url: window.root_path + "/ajax/isClientAuthenticad",
                                type: 'GET',
                                success: function (data) {
                                    var returned = $.parseJSON(data);

                                    if (returned.retorno > 0) {
                                        var optionsAjax = {
                                            title: 'Confirma Logout?',
                                            text: 'Você já está logado em outra conta em outra aba ou em outro navegador. Ao avançar no cadastro o sistema vai encerrar essa sessão e qualquer informação não salva no navegador será perdida, deseja avançar?',
                                            type: "warning",
                                            showCancelButton: true,
                                            confirmButtonClass: "btn-success",
                                            confirmButtonText: "Sim",
                                            cancelButtonText: "Não"
                                        };

                                        swal(optionsAjax, function (isConfirm) {
                                            if (isConfirm) {
                                                form.submit();
                                            } else {
                                                $submitBtn.prop('disabled', false);
                                                removeLoaderFromElement($submitBtn);
                                                return false;
                                            }
                                        });
                                    } else {
                                        form.submit();
                                    }
                                }
                            });
                        } else {
                            $submitBtn.prop('disabled', false);
                            removeLoaderFromElement($submitBtn);
                            return false;
                        }
                    });
                } else {
                    var options = {
                        title: 'Confirmação Patrocinador',
                        text: 'Seu patrocinador será adicionado aleatoriamente, você confirma isso?',
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Sim",
                        cancelButtonText: "Não"
                    };

                    swal(options, function (isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                url: window.root_path + "/ajax/isClientAuthenticad",
                                type: 'GET',
                                success: function (data) {
                                    var returned = $.parseJSON(data);

                                    if (returned.retorno > 0) {
                                        var optionsAjax = {
                                            title: 'Confirma Logout?',
                                            text: 'Você já está logado em outra conta em outra aba ou em outro navegador. Ao avançar no cadastro o sistema vai encerrar essa sessão e qualquer informação não salva no navegador será perdida, deseja avançar?',
                                            type: "warning",
                                            showCancelButton: true,
                                            confirmButtonClass: "btn-success",
                                            confirmButtonText: "Sim",
                                            cancelButtonText: "Não"
                                        };

                                        swal(optionsAjax, function (isConfirm) {
                                            if (isConfirm) {
                                                form.submit();
                                            } else {
                                                $submitBtn.prop('disabled', false);
                                                removeLoaderFromElement($submitBtn);
                                                return false;
                                            }
                                        });
                                    } else {
                                        form.submit();
                                    }
                                }
                            });
                        } else {
                            $submitBtn.prop('disabled', false);
                            removeLoaderFromElement($submitBtn);
                            return false;
                        }
                    });
                }

                $submitBtn.prop('disabled', false);
                removeLoaderFromElement($submitBtn);
                return false;
            });
        });
    </script>
<?php else : ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $("button[type='submit']").on('click', function (e) {

                e.preventDefault();
                var $submitBtn = $(this);
                var form = $(this).closest('form');

                $.ajax({
                    url: window.root_path + "/ajax/isClientAuthenticad",
                    type: 'GET',
                    success: function (data) {
                        var returned = $.parseJSON(data);

                        if (returned.retorno > 0) {

                            var optionsAjax = {
                                title: 'Confirma Logout?',
                                text: 'Você já está logado em outra conta em outra aba ou em outro navegador. Ao avançar no cadastro o sistema vai encerrar essa sessão e qualquer informação não salva no navegador será perdida, deseja avançar?',
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonClass: "btn-success",
                                confirmButtonText: "Sim",
                                cancelButtonText: "Não"
                            };

                            swal(optionsAjax, function (isConfirm) {
                                if (isConfirm) {
                                    form.submit();
                                } else {
                                    $submitBtn.prop('disabled', false);
                                    removeLoaderFromElement($submitBtn);
                                    return false;
                                }
                            });

                        } else {
                            form.submit();
                        }

                    }
                });
            });
        });

    </script>
<?php endif; ?>
<script>
    $(document).ready(function () {
        $('body').on('click', '#pagamento-cadastro', function (e) {
            $('#cadastro').hide('fast');
            $('#endereco').show('fast');
        });

        $('body').on('click', '#editar-cadastro', function (e) {
            $('#endereco').hide('fast');
            $('#cadastro').show('fast');
        });

    });
</script>
</body>
</html>
