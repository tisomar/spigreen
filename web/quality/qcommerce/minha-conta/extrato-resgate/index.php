<?php

require __DIR__ . '/actions/extrato-resgate.actions.php';

use QPress\Template\Widget;

$strIncludesKey = 'minha-conta-extrato-resgate';
include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";
?>

<main role="main">

    <input type="hidden" id="idCliente" value="<?php echo $clienteId ?>">

    <body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-extrato-resgate">
    <?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
    <?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
    <?php Widget::render('general/header'); ?>

    <?php
    Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Extrato de Solicitação de Resgate' => '')));
    Widget::render('general/page-header', array('title' => 'Extrato de Solicitação de Resgate'));
    Widget::render('components/flash-messages');
    ?>

    <div class="container">
        <div class="row">
            <!-- INÍCIO -->
            <div class="col-xs-12 col-md-3">
                <?php Widget::render('general/menu-account', array('strIncludesKey' => $strIncludesKey)); ?>
            </div>

            <?php /** @var $object Resgate */ ?>

            <div>
                <div>
                    <!-- TODO: Histórico de Resgate -->
                </div>
                <form action="">
                    <div class="form-group col-xs-12 col-md-9">
                        <!-- <select name="" id="resgate">
                            <option value="">Resgates</option>
                            </*?php
                                echo $optionResgate;
                            ?>-->
                        <!-- <br> -->
                        <!-- <option value=""></*?php var_dump($resgate) ?></option> -->

                        <!-- Início form select resgate -->
                        <div class="col-xs-12">
                            <div class="form-group">
                                <select class="form-control resgate" id="resgate" name="solicitacao_resgate">
                                    <option value="">Resgates...</option>
                                    <?php echo $optionResgate ?>
                                </select>
                                <br>

                                <table class="table tableResgate" hidden>
                                    <thead>
                                    <tr>
                                        <th>Titulo</th>
                                        <th>Valor:</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Bônus solicitados:</td>
                                        <td id='bonus-solicitados'></td>
                                    </tr>
                                    <tr>
                                        <td>Data:</td>
                                        <td id='data'></td>
                                    </tr>
                                    <tr>
                                        <td>Taxa cobrada:</td>
                                        <td id='taxa-cobrada'></td>
                                    </tr>
                                    <tr>
                                        <td>Banco:</td>
                                        <td id='banco'></td>
                                    </tr>
                                    <tr>
                                        <td>Agência:</td>
                                        <td id='agencia'></td>
                                    </tr>
                                    <tr>
                                        <td>Conta:</td>
                                        <td id='conta'></td>
                                    </tr>
                                    <tr>
                                        <td>Pis/Pasep:</td>
                                        <td id='pis-pasep'></td>
                                    </tr>
                                    <tr>
                                        <td>Nome correntista:</td>
                                        <td id='nome-correntista'></td>
                                    </tr>
                                    <tr>
                                        <td>CPF correntista:</td>
                                        <td id='cpf-correntista'></td>
                                    </tr>
                                    <tr>
                                        <td>Situação:</td>
                                        <td id='situacao'></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Fim form select -->
                    </div>
                </form>
                <!-- <div class="panel-body">
                    </*?php foreach ($list as $object) : ?>
                        <div class="col-xs-12 col-md-9">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td><b>Bônus solicitados</b></td>
                                        <td></*?= formata_valor($object->getValor()) ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Taxa cobrada</b></td>
                                        <td></*?= formata_valor($object->getValorTaxa()) ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Data</b></td>
                                        <td></*?= $object->getData('d/m/Y') ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Banco</b></td>
                                        <td></*?= $object->getBanco() ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Agência</b></td>
                                        <td></*?= $object->getAgencia() ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Conta</b></td>
                                        <td></*?= $object->getConta() ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Tipo Conta</b></td>
                                        <td></*?= $object->getTipoConta() === Resgate::CONTA_POUPANCA ? 'Poupança' : 'Conta Corrente' ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Pis/Pasep</b></td>
                                        <td></*?= $object->getPisPasep() ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Nome correntista</b></td>
                                        <td></*?= $object->getNomeCorrentista() ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>CPF correntista</b></td>
                                        <td></*?= $object->getCpfCorrentista() ?></td>
                                    </tr>
                                    <tr>
                                        <td><b>Situação</b></td>
                                        <td></*?= $object->getSituacao() ?></td>
                                    </tr>
                                <tbody>
                            </table>
                        </div>
                    </*?php endforeach; ?>
                </div>
            </div> -->
                <!-- FIM -->
            </div>
        </div>
</main>
<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>
</body>

<script>

    $(document).ready(function () {
        $('.resgate').change(function () {
            let resgates = $(this).val();
            console.log(resgates);
            if (resgates != '') {
                $('.tableResgate').attr('hidden', false);
            } else {
                $('.tableResgate').attr('hidden', true);
            }
            $.ajax({
                url: window.root_path + '/ajax/ajax-resgates/',
                data: {
                    resgates: resgates
                },
                type: 'POST',
                success: function (data) {
                    let obj = JSON.parse(data);
                    console.log(obj);
                    $('#bonus-solicitados').html(obj.VALOR);
                    $('#taxa-cobrada').html(obj.VALOR_TAXA);
                    $('#data').html(obj.DATA);
                    $('#banco').text(obj.BANCO);
                    $('#agencia').text(obj.AGENCIA);
                    $('#conta').text(obj.CONTA);
                    $('#tipo-conta').text(obj.TIPO_CONTA);
                    $('#pis-pasep').text(obj.PIS_PASEP);
                    $('#nome-correntista').text(obj.NOME_CORRENTISTA);
                    $('#cpf-correntista').text(obj.CPF_CORRENTISTA);
                    $('#situacao').text(obj.SITUACAO);
                }
            });
        });
    });

</script>
