<?php
require __DIR__ . '/actions/resgate.actions.php';
use QPress\Template\Widget;
$strIncludesKey = 'minha-conta-resgate';
include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";
?>

    <body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-resgate">
    <?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
    <?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
    <?php Widget::render('general/header'); ?>

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
                                Solicitar Resgate.
                            </h3>
                        </div>
                    </div>
                    <br>
                                        
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <span class="<?php icon('info'); ?>"></span>

                                    Total de bônus disponíveis R$ <strong><?php echo formata_pontos($pontosDisponiveis) ?></strong>
                                </div>
                            </div>       
                        </div>
                        <div class="col-xs-12">
                            <div class="panel panel-default" style="background-color: #efefef;">
                                <div class="panel-body">
                                    <?php echo Config::get('resgate.texto_editor') ?>
                                </div>
                            </div>
                        </div>
                        <?php if ($bloqueiaResgate): ?>
                            <div class="col-xs-12 text-center">
                                Realize a sua ativação mensal para efetuar resgates.
                            </div>
                        <?php elseif ($pontosDisponiveis >= $pontosMinimo) : ?>

                            <div class="col-xs-12">
                                <div class="panel panel-default" style="background-color: #efefef;">
                                    <div class="panel-body">

                                        <?php 
                                        $resgatePeríodo =  strftime('%d', strtotime('today')); 
                                        if($resgatePeríodo <= 15):?>
                                           <div class="row">
                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="form-group">
                                                        <button class="btn btn-success btn-sm btn-block" type="button" data-toggle="modal" data-target="#myModal"> 
                                                            Cadastrar banco
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="btn-toolbar pull-right text-right" role="toolbar" aria-label="Toolbar with button groups">
                                                        <div class="btn-group mr-2" role="group" aria-label="First group">
                                                            <button type="button" class="btn btn-success btn-sm pull-right" style="margin-right: 3px;" id='val_max'>maximo</button>
                                                            <button type="button" class="btn btn-success btn-sm pull-right" style="margin-right: 8px;" id='val_min'>mínimo</button>
                                                        </div>
                                                        <div class="btn-group mr-2" role="group" aria-label="Second group">
                                                            <form role="form" method="post" class="form-disabled-on-load formBancoResgate">
                                                                <div class="form-group">
                                                                    <input style="max-width:30vmin" type="text" class="mask-money form-control input-sm" id="qtd-pontos" required name="resgate[VALOR]" min="1" max="<?php echo (int)$totalPontosDisponiveis + PHP_INT_MAX ?>" placeholder="<?php echo 'valor ' . escape($arrResgate['VALOR']) ?>">
                                                                    <input type="hidden" id='idBancoResgate' name="resgate[ID]">
                                                                </div>
                                                            </form>   
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class='row'>
                                                <?php foreach($BancosCadastrados as $banco):?>
                                                    <div class="col-sm-6 col-xs-12 box_<?php echo $banco->getId() ?>" >
                                                        <div class="card box-card-banco">
                                                            <div class="card-body">
                                                                <h5 class="card-title">
                                                                    <span> <?php echo $banco->getBanco() ?></span>
                                                                    <button type="button" style="color:#A1E63A" value="<?php echo $banco->getId()?>" class="<?php icon('remove'); ?> pull-right excluiBanco"></button>
                                                                    <button style="color:#A1E63A" type="button" class="<?php icon('edit'); ?> pull-right " data-toggle="modal" data-target="#myModal" data-banco-id="<?= $banco->getId() ?>"></button>
                                                                </h5>

                                                                <p class="card-text">Nome: <?php echo $banco->getNomeCorrentista() ?> <br> <?php echo $banco->getCpf() != null ? 'CPF: '. $banco->getCpf() : 'CNPJ: ' . $banco->getCnpj() ?></p>
                                                                <button class="btn btn-success btn-sm resgatar" value="<?php echo $banco->getId()?>">Resgatar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach ?>
                                            </div>    
                                        <?php else:
                                            echo '<br> Os resgates estão disponíveis somente do dia 1 ao dia 15 de cada mês';
                                        endif; ?>
                                    </div>    
                                </div>
                            </div>

                            <!-- // MODAL CADASTRO DE BANCO -->
                            <div class="col-xs-12">
                                <div class="modal fade" id="myModal" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content" >
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                    <?php 
                                                        \QPress\Template\Widget::render('forms/cadastroBanco', array(
                                                            'totalPontosDisponiveis' => $totalPontosDisponiveis,
                                                            'arrResgate' => $arrResgate,
                                                            'resgateDesabilitado' => $resgateDesabilitado
                                                        ));
                                                    ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php else : ?>
                            <div class="col-xs-12">
                                <?php echo Config::get('resgate.sem_saldo') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>
        .modal-dialog {
            height: auto;
            width: 100%;
            max-width: 500px;
            margin: 2% auto;
            position: relative;
        }
        .box-card-banco{
            margin-top:3vmin; border: 1px solid #D3D3D3; border-radius:12px; padding: 3vmin;
        }
    </style>

    <script type="text/javascript">
        $(document).ready(function () {

            $('.excluiBanco').click(function() {
                let idBanco = $(this).val();
                var excluir = confirm('Deseja realmente excluir este cadastro?');
                if (excluir == true) {
                    $.ajax({
                        url: '/ajax/ajax_delete_banco',
                        dataType: 'json',
                        type: 'POST',
                        data: {idBanco: idBanco},
                        success: function(data) {
                            console.log(data)
                            if(data.response == '200') {
                                $('.box_'+idBanco).remove();
                            }
                        }
                    })
                }

            })

            $('#cnpj').blur(function () {
                var cnpj = $("input#cnpj").val();

                if (cnpj != '') {
                    if(!validarCNPJ(cnpj)) {
                        alert("O CNPJ inserido não e válido");
                        $('input#cnpj').val("");
                        $('input#cnpj').focus();
                    }
                }
            });

            function validarCNPJ(cnpj) {
                cnpj = cnpj.replace(/[^\d]+/g,'');

                if (cnpj.length != 14)
                    return false;

                // Elimina CNPJs invalidos conhecidos
                if (cnpj == "00000000000000" ||
                    cnpj == "11111111111111" ||
                    cnpj == "22222222222222" ||
                    cnpj == "33333333333333" ||
                    cnpj == "44444444444444" ||
                    cnpj == "55555555555555" ||
                    cnpj == "66666666666666" ||
                    cnpj == "77777777777777" ||
                    cnpj == "88888888888888" ||
                    cnpj == "99999999999999")
                    return false;

                // Valida DVs
                tamanho = cnpj.length - 2
                numeros = cnpj.substring(0,tamanho);
                digitos = cnpj.substring(tamanho);
                soma = 0;
                pos = tamanho - 7;
                for (i = tamanho; i >= 1; i--) {
                    soma += numeros.charAt(tamanho - i) * pos--;
                    if (pos < 2)
                        pos = 9;
                }
                resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
                if (resultado != digitos.charAt(0))
                    return false;

                tamanho = tamanho + 1;
                numeros = cnpj.substring(0,tamanho);
                soma = 0;
                pos = tamanho - 7;
                for (i = tamanho; i >= 1; i--) {
                    soma += numeros.charAt(tamanho - i) * pos--;
                    if (pos < 2)
                        pos = 9;
                }
                resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
                if (resultado != digitos.charAt(1))
                    return false;

                return true;
            }

            $('#cpf, #cnpj').on('input', function(e) {
                var value = this.value;
                let idCliente = $('#idCliente').val();

                if (value) {
                    $(e.target.id == 'cpf' ? '#cpf' : '#cnpj').attr('required', true);
                    $(e.target.id == 'cpf' ? '#cnpj' : '#cpf').attr('required', false);

                    $('#pispaseo').attr('required', false);
                    
                    if(e.target.id == 'cpf' && idCliente != 8) {
                        $('#pispaseo').attr('required', true);
                    }

                } else {
                    $('#cpf').attr('required', true);
                    $('#cnpj').attr('required', true);
                }
            });

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

            $('#val_min').click(function() {
                $('#qtd-pontos').val("<?php echo $pontosMinFormatMoney?>")
            })

            $('#val_max').click(function() {
                $('#qtd-pontos').val("<?php echo $totalPontosDisponiveis?>")
            })

            $('#myModal').on('show.bs.modal', function (e) {
                var target = $(e.relatedTarget);
                var idBanco = target.data('bancoId');
                
                if (idBanco) {
                    console.log('ttest', idBanco);
                    $.ajax({
                        url: '/ajax/ajax_banco',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            idBanco: target.data('bancoId')
                        },
                        success: function(data) {
                            var form = $('form.form-banco', target.data('target'));
                            if (data) {
                                $('#banco').val(data.Banco);
                                $('#agencia').val(data.Agencia);
                                $('#conta').val(data.Conta);
                                $('#tipo_conta').val(data.TipoConta);
                                $('#pispaseo').val(data.PisPasep);
                                $('#nome').val(data.NomeCorrentista);
                                $('#cpf').val(data.Cpf);
                                $('#idBanco').val(data.Id);
                            }
                        }
                    })
                } else {
                    $('#banco').val('');
                    $('#agencia').val('');
                    $('#conta').val('');
                    $('#pispaseo').val('');
                    $('#nome').val('');
                    $('#cpf').val('');
                    $('#idBanco').val('');
                    $('#idBanco').val('');
                }
            });

            $('#cpf').blur(function () {
                var cpf = $("input#cpf").val();

                if (cpf != '') {
                    if(!validarCPF(cpf)) {
                        alert("O CPF inserido não e válido");
                        $('input#cpf').val("");
                        $('input#cpf').focus();
                    }
                }
            });

            function validarCPF(cpf) {
                cpf = cpf.replace(/[^\d]+/g, '');
                if (cpf == '') return false;
                // Elimina CPFs invalidos conhecidos
                if (cpf.length != 11 ||
                    cpf == "00000000000" ||
                    cpf == "11111111111" ||
                    cpf == "22222222222" ||
                    cpf == "33333333333" ||
                    cpf == "44444444444" ||
                    cpf == "55555555555" ||
                    cpf == "66666666666" ||
                    cpf == "77777777777" ||
                    cpf == "88888888888" ||
                    cpf == "99999999999")
                    return false;
                // Valida 1o digito
                var add = 0;
                for (i = 0; i < 9; i++)
                    add += parseInt(cpf.charAt(i)) * (10 - i);
                var rev = 11 - (add % 11);
                if (rev == 10 || rev == 11)
                    rev = 0;
                if (rev != parseInt(cpf.charAt(9)))
                    return false;
                // Valida 2o digito
                add = 0;
                for (i = 0; i < 10; i++)
                    add += parseInt(cpf.charAt(i)) * (11 - i);
                rev = 11 - (add % 11);
                if (rev == 10 || rev == 11)
                    rev = 0;
                if (rev != parseInt(cpf.charAt(10)))
                    return false;
                return true;
            }
        });
    </script>

    <?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>

    <?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>
    </body>

</html>
