<?php

use QPress\Template\Widget;

$strIncludesKey = 'minha-conta-ticket';
include QCOMMERCE_DIR . '/includes/security.php';
include QCOMMERCE_DIR . '/minha-conta/ticket/actions/atendimentos.actions.php';
include QCOMMERCE_DIR . '/includes/head.php';
?>

<body itemscope itemtype="http://schema.org/WebPage">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<style>
    .header-atendimento{
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .header-atendimento button {
        background: #63b876;
    }

    .header-atendimento button:hover{
        background: #FFF;
        color: #000;
    } 

    #myModal,
    #myModalChat{
        overflow: hidden;
        height: auto;
        margin: auto;
        width: 40vw;
    }

    #myModal .modal-header,
    #myModalChat .modal-header{
        background-color: #edeef0;
        border-top: .5px solid #ccc;
        border-bottom: .1px solid #e4e4e4;
    }

    #myModal .modal-header h4,
    #myModalChat .modal-header h4 {
        font-weight: bold;
    }

    #myModal .close,
    #myModalChat .close{
        width: 10px;
        font-weight: bold;
        margin-top: -25px;
    }

    #myModal .modal-body h3,
    #myModalChat .modal-body h3 {
        margin-left: 15px;
        font-weight: bold;
    }

    #myModal .modal-body .ticket-type{
        margin: auto;
        width: 95%;
        border: 1px solid #ccc;
        border-radius: 4px;

        display: flex;
        flex-direction: column; 
        cursor: pointer;
    }

    #myModal .modal-body .ticket-type .ticket-item:hover {
        border-left: 3px solid #63b876;
        border-radius: 4px;
    }

    .modal-footer{
        margin-bottom: 0px;
    } 

    .modal-footer button{
        background: #63b876;
        border: 0;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 30px;
        color: #FFF;
        font-weight: bold;
    }

    .modal-footer button.btnVoltar {
        background: #FFF;
        border: 1px solid #ddd;
        color: #000;
    }

    .modal-footer button.btnVoltar:hover {
        background: #f9f9f9;
    } 

    .modal-footer .disabled{
        background: #ddd;
    }

    .modal-footer .disabled:hover{
        background: #333;
    }

    .modal-footer button:hover{
        background: #63b888;
    }

    .selected {
        background: #f9f9f9;
    }

    .selected .ticket-item{
        border-left: 3px solid #63b876;
        border-radius: 4px;
    }

    blockquote{
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        background: #f9f9f9;
        cursor: pointer;
    }

    .legenda,
    .legenda .emandamento,  
    .legenda .pendente,
    .legenda .finalizado  {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }

    .legenda .emandamento div,
    .legenda .pendente div,
    .legenda .finalizado div {
        width: 20px;
        height: 20px;
        margin-right: 5px;
        border-radius: 3px;
    }

    .labelEmandamento{
        background-color: #f5f231;
    }

    .labelfinalizado{
        background-color: #63b876;
    }

    .labelpendente{
        background-color: rgb(221, 111, 119);
    }

    .FINALIZADO {
        border-color: #63b876;
        border-radius: 4px;
    }

    .PENDENTE {
        border-color: rgb(221, 111, 119);
        border-radius: 4px;
    }

    .EMANDAMENTO {
        border-color: #f5f231;
        border-radius: 4px;
    }


    #myModalChat .messagesBox{
    }

    #myModalChat .messagesBox .userInfo{
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }

    .boxMensagens{
        height: 100vw
        /* height: 100vw; */
    }

    .modal-dialog {
        /* height: 60vw !important; */
        background:#FFF;
        overflow: auto;
    }

    .modal-content {
        box-shadow: none !important;
    }


    @media only screen and (max-width: 600px) {
        #myModal, #myModalChat {
            width: 100vw !important;
        }

        blockquote, 
        .legenda,
        .legenda .emandamento,  
        .legenda .pendente,
        .legenda .finalizado {
            flex-direction: column;
        }
    }

    .textAreaMessage{
        padding: 0 !important;
    }

    .descricao{
        text-align: center;
    }

    .descricao span{
        color: #63b876;
    }

</style>    

<!-- <link rel="stylesheet" type="text/css" href="http://bootstrap-wysiwyg.github.io/bootstrap3-wysiwyg/components/bootstrap/dist/css/bootstrap-theme.min.css"></link> -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

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
                <h4>Solicita????o de atendimento
                </h4>
                <br>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-body header-atendimento">
                                <span class="<?php icon('info'); ?> descricao">
                                    Cadastre uma solicita????o de atendimento. <br>
                                    <span>Sua solicita????o ser?? iniciada at?? o pr??ximo dia ??til</span>
                                </span> 

                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                    Registrar atendimento
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                  <!-- The Modal -->
                <div class="modal" id="myModalChat">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        
                            <div class="modal-header">
                                <h4 class="modal-title">Registro de mensagem</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            
                            <div class="modal-body boxMensagens">
                                <div class="messagesMain">
                                    
                                </div>
                                <div class="form-group col-md-12 textAreaMessage hidden-xs">
                                    <textarea class="textarea form-control" id="novaMensagem" name="novaMensagem" rows="1"></textarea><br>
                                    <button class="btn btn-success pull-right btnSendMessage" type="button">Enviar</button>
                                </div>
                                
                                <div class="form-group col-md-12 textAreaMessage hidden-sm hidden-lg">
                                    <textarea class="textareaXS form-control" id="novaMensagemXS" name="novaMensagem" rows="1"></textarea><br>
                                    <button class="btn btn-success pull-right btnSendMessage" type="button">Enviar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                                    
                 <!-- The Modal -->
                 <div class="modal" id="myModal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        
                            <div class="modal-header">
                                <h4 class="modal-title">Registrar Atendimento</h4>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            
                            <form action="" method="POST" id="form-enviar-ticket">
                                <input type="hidden" id="categoria" name="categoria">
                                <input type="hidden" id="assunto" name="assunto">
                                <input type="hidden" id="email" name="email">
                                <input type="hidden" id="grupoId" name="grupoId">
                                <input type="hidden" id="ticketSelecionado" name="ticketSelecionado">

                                <div class="modal-body">
                                    <h3>Escolha o tipo de ticket e clique em "AVAN??AR"</h3>
                                    
                                    <div class="step1 steps">
                                        <div class="row row-eq-height ticket-type" id="Comercial" data-target="Comercial">
                                            <div class="col-xs-12 ticket-item">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <h4>Comercial</h4>
                                                        <p>Est?? com alguma d??vida de configura????o ou funcionamento do sistema? Entre em contato atrav??s desta op????o e nossa equipe ir?? lhe ajudar..</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><br>

                                        <div class="row row-eq-height ticket-type" id="Financeiro" data-target="Financeiro">
                                            <div class="col-xs-12 ticket-item">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <h4>Financeiro</h4>
                                                        <p> Utilize esta op????o e voc?? estiver com alguma dificuldade relacionada ao pagamento de seus pedidos. </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><br>

                                        <div class="row row-eq-height ticket-type" id="Jur??dico" data-target="Jur??dico">
                                            <div class="col-xs-12 ticket-item">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <h4>Jur??dico</h4>
                                                        <p> Se busca informa????es sobre tributa????es ou reten????es quanto a pessoa jur??dica ou pessoa f??sica, escolha esta op????o. </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><br>

                                        <div class="row row-eq-height ticket-type" id="Logistica" data-target="Logistica">
                                            <div class="col-xs-12 ticket-item">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <h4>Log??stica</h4>
                                                        <p> Est?? com algum problema em rela????o a entrega ou pedido atrasado entre em contato atrav??s desta op????o. </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><br>

                                        <div class="row row-eq-height ticket-type" id="Sugest??es" data-target="Sugest??es">
                                            <div class="col-xs-12 ticket-item">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <h4>Sugest??es</h4>
                                                        <p> Voc?? tem alguma sugest??o de melhoria para o sistema? Utilize esta op????o e entre em contato conosco. As sugest??es s??o analisadas e poder??o ser utilizadas em futuras melhorias. </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><br>

                                        <div class="row row-eq-height ticket-type" id="Ti" data-target="Ti">
                                            <div class="col-xs-12 ticket-item">
                                                <div class="row">
                                                    <div class="col-xs-12">
                                                        <h4>Ti</h4>
                                                        <p>Caso o sistema apresente algum comportamento imprevisto entre em contato atrav??s desta op????o.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><br>

                                        <div class="modal-footer">
                                            <button type="button" class="disabled go-to-step-2" data-target="step2">Avan??ar</button>
                                        </div>
                                    </div>
                                    <!-- -  Por favor, descreva a Natureza da Quest??o e cite exemplos relevantes -->
                                    <!-- -  N??o recebeu o pedido / Atraso na  entrega / c??digo de rastreamento -->

                                    <div class="step2 steps" hidden>
                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label for="inputAssunto">Topicos</label>
                                                <select id="inputAssunto" class="form-control">
                                                    <option selected>Selecione...</option>
                                                </select>                                  
                                            </div>

                                            <div class="form-group col-md-12">
                                                <label for="exampleFormControlTextarea1">Descri????o do ticket</label>
                                                <textarea class="form-control" id="descricao-ticket" name="descricao-ticket" rows="3"></textarea>
                                            </div>
                                        </div>
                                    
                                        <div class="modal-footer">
                                            <button type="button" class="disabled go-to-step-1 btnVoltar" data-target="step1">Voltar</button>
                                            <button type="button" class="disabled" id="finish-ticket">Enviar</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <form action="<?php echo get_url_site() . '/minha-conta/ticket/' ?>" role="form" method="get" class="form-disabled-on-load">
                    <?php Widget::render('forms/filtro-ticket',
                        array(
                            'dtInicio' => $dtInicio,
                            'dtFim' => $dtFim
                        )); 
                    ?>
                    
                    <div class="col-xs-12" style="margin-bottom: 15px;">
                        <div class="row">
                            <small class="text-muted">
                                Obs.: Datas dos tickets s??o referentes ao hor??rio de Bras??lia.
                            </small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-theme btn-block">Filtrar</button>
                    </div>
                </form>

                <?php if (count($pager) > 0) : ?>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="pull-left">
                                <span class="pull-left">
                                   <p>Meus atendimento</p>
                                </span>
                                <span class="pull-left"><?php echo resumo('test', 2) ?></span>

                            </h4>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">

                            <?php foreach($pager as $ticket) :?>
                                <b><?php echo $ticket->getCategoria() . ' - Ticket n??  #' . $ticket->getId() ?></b>

                                <blockquote class="<?php echo $ticket->getStatus()?> openChat" data-email="<?= $ticket->getEmailDestino() ?>" data-assunto="<?= $ticket->getAssunto()?>" data-target="<?php echo $ticket->getId() ?>" data-status="<?php echo $ticket->getStatus() ?>">    
                                    Assunto: <?php echo $ticket->getAssunto() ?>
                                    <span class="pull-left"><?php echo resumo($ticket->getDescricao(), 30) ?></span>
                                    <small><?php echo $ticket->getData('d/m/Y ??\s H\hi')?></small> 
                                </blockquote>
                            
                            <?php endforeach; ?>

                            <div class="legenda">
                                <div class="emandamento">
                                    <div class="labelEmandamento"></div>
                                    <p>Em andamento</p>
                                </div>

                                <div class="finalizado">
                                    <div class="labelfinalizado"></div>
                                    <p>Finalizado</p>
                                </div>

                                <div class="pendente">
                                    <div class="labelpendente"></div>
                                    <p>Pendente</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                        Widget::render('components/pagination', array(
                            'pager' => $pager,
                            'href' => get_url_site() . '/minha-conta/ticket/',
                            'queryString' => $queryString,
                            'align' => 'center'
                        ));
                    ?>
                <?php else : ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <span class="<?php icon('info'); ?>"></span> Voc?? n??o efetuou nenhum coment??rio sobre os produtos at?? este momento.
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</main>
<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>

<script src="<?= asset('/js/libs/summernote/summernote-lite.min.js') ?>"></script>

<script>
    $(document).ready(function(){   

        $('.textarea').summernote({

            tabsize: 2,
            height: 120,
            lang: 'pt-BR',
            toolbar: [
                ['basic', ['style', 'fontname', 'fontsize']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture']],
                // ['table', ['table']],
                // ['insert', ['link', 'picture', 'video']],
                // ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        $('.textareaXS').summernote({

            tabsize: 2,
            height: 120,
            lang: 'pt-BR',
            toolbar: [
                ['basic', ['style', 'fontname', 'fontsize']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
            ]
        });

        function htmlAssunto(topico) {
            var assuntoHtml = '';
            var email = '';
            var grupoID = 1;

            switch (topico) {
                case 'Logistica':
                    assuntoHtml += 
                        '<option value="">Selecione o topico ...</option>' +
                        '<option value="Pedido faltando item">Pedido faltando item</option>' +
                        '<option value="Entregas">Entregas</option>';

                    // email = 'logistica@spigreen.com.br';
                    email = 'ticket@spigreen.com.br';
                    grupoId = 8;
                    break;
                case 'Marketing':
                    assuntoHtml += 
                        '<option value="">Selecione o topico ...</option>' +
                        '<option value="Comunicados SPIGREEN">Comunicados SPIGREEN</option>' +
                        '<option value="Eventos">Eventos</option>' +
                        '<option value="SPI DIGITAL">SPI DIGITAL</option>' +
                        '<option value="Promo????es">Promo????es</option>' +
                        '<option value="Publicidade e Marca">Publicidade e Marca</option>' +
                        '<option value="Rela????es com Imprensa">Rela????es com Imprensa</option>';

                    // email = 'marketing@spigreen.com.br';
                    email = 'ticket@spigreen.com.br';
                    grupoId = 7;
                    break;
                case 'Comercial':
                    assuntoHtml += 
                        '<option value="">Selecione o topico ...</option>' +
                        '<option value="Promo????es">Promo????es</option>' +
                        '<option value="Informa????es gerais">Informa????es gerais</option>' +
                        '<option value="Minhas informa????es de Distribuidor">Minhas informa????es de Distribuidor</option>' +
                        '<option value="Produtos">Produtos</option>' +
                        '<option value="Sugest??o">Sugest??es</option>';

                    email = 'ticket@spigreen.com.br';
                    grupoId = 9;
                    break;
                case 'Jur??dico':
                    assuntoHtml += 
                        '<option value="">Selecione o topico ...</option>' +
                        '<option value="Tributa????o e reten????es / Pessoa Jur??dica">Tributa????o e reten????es/ Pessoa Jur??dica</option>' +
                        '<option value="Tributa????es e reten????es / pessoa f??sica">Tributa????es e reten????es / pessoa f??sica</option>';

                    // email = 'juridico@spigreen.com.br';
                    email = 'ticket@spigreen.com.br';

                    grupoId = 1;
                    break;
                case 'Financeiro':
                    assuntoHtml += 
                        '<option value="">Selecione o topico ...</option>' +
                        '<option value="Devolu????o">Devolu????o</option>' +
                        '<option value="Pedido aguardando pagamento">Pedido aguardando pagamento</option>';
                    
                    // email = 'financeiro@spigreen.com.br';
                    email = 'ticket@spigreen.com.br';

                    grupoId = 6;
                    break;
                case 'Sugest??es':
                    assuntoHtml += 
                        '<option value="">Selecione o topico ...</option>' +
                        '<option value="Sugest??o">Sugest??o</option>';
                    
                    // email = 'ernani.braga@spigreen.com.br';
                    email = 'ticket@spigreen.com.br';

                    grupoId = 1;
                    break;
                case 'Ti':
                    assuntoHtml += 
                        '<option value="">Selecione o topico ...</option>' +
                        '<option value="Mau funcionamento">Mau funcionamento</option>' + 
                        '<option value="D??vida">D??vida</option>';

                    // email = 'ti@spigreen.com.br';
                    email = 'ticket@spigreen.com.br';

                    grupoId = 3;
                    break;
            }

            $('#inputAssunto').html(assuntoHtml);
            $('#email').val(email);
            $('#grupoId').val(grupoId);
        }
    
        $('.ticket-type').click(function() {
            $('.ticket-type').removeClass('selected');
            const categoria = $(this).data('target');
            htmlAssunto(categoria);

            $('#' + categoria).addClass('selected');
            $('#categoria').val(categoria);

            if($('#categoria').val() !== '') {
                $('.modal-footer button').removeClass('disabled');
            }
        })

        $('.modal-footer button').on('click', function() {
            const step = $(this).data('target');
            $('.steps').attr('hidden', true);
            $('.'+ step).attr('hidden', false);
        })
      
        $('#inputAssunto').on('change', function() {
            const assunto = $(this).val();
            $('#assunto').val(assunto);
        });

        $('#pessoaFisica').click(function() {

            var optionsAjax = {
                title: 'Confirma????o?',
                text: 'Voc?? deseja realmente remover seu cadastro de pessoa jur??dica?',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Sim",
                cancelButtonText: "N??o"
            };

            swal(optionsAjax, function (isConfirm) {
                if (isConfirm) {
                    $('#removeCNPJ').val('1');
                    $('#mostraFormPJ').attr('hidden', true);
                }
            });
        })

        $('#finish-ticket').on('click', function() {
            $('#form-enviar-ticket').submit();
        })

        $('.openChat').on('click', function() {
            const ticketId = $(this).data('target');
            $('#ticketSelecionado').val(ticketId);
            $('#assunto').val($(this).data('assunto'));
            $('#email').val($(this).data('email'));
            
            $('.textAreaMessage').attr('hidden', false);

            if($(this).data('status') === 'FINALIZADO') {
                $('.textAreaMessage').attr('hidden', true);
            }

            openModalMessages(ticketId);
        })

        function openModalMessages(ticketId) {
            $.ajax({
                url: '/ajax/ajax_mensagens_ticket',
                type: "POST",
                data: {ticketId: ticketId},
                dataType: "json",
                success: function (response) {
                    $('.messagesMain').html(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });

            $('#myModalChat').modal('show');
        }

        $('#myModalChat .modal-body .btnSendMessage').on('click', function() {
            const ticketId = $('#ticketSelecionado').val();
            const mensagem = $('#novaMensagem').val() != '' ?  $('#novaMensagem').val() : $('#novaMensagemXS').val();
            const assunto = $('#assunto').val();
            const remetente = 'CLIENTE';
            const emailDestino = $('#email').val();
            const remetenteNome = "<?php echo $clienteLogado->getNomeCompleto() ?>";

            $.ajax({
                url: '/ajax/ajax_mensagens_novo_ticket',
                type: "POST",
                data: {
                    ticketId: ticketId, 
                    mensagem: mensagem,
                    remetente: remetente,
                    remetenteNome: remetenteNome,
                    assunto: assunto,
                    emailDestino: emailDestino
                },
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    if(response.status === 'ok') {
                        $('.note-editable').text('');
                        $('#novaMensagem').val();
                        openModalMessages(ticketId);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    swal({
                        title: "Erro!",
                        text: "Aconteceu algum erro ao inserir a imagem!",
                        icon: "warning",
                    });
                }
            });
        })
    });
</script>
</body>
</html>