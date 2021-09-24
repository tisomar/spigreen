<?php
use PFBC\View;
use PFBC\Form;
use PFBC\Element;
$form = new Form("registrer");

$statusDescricao = array(
    TicketPeer::STATUS_FINALIZADO => 'Atendimento finalizado',
    TicketPeer::STATUS_PENDENTE => 'Atendimento pendente',
    TicketPeer::STATUS_EM_ANDAMENTO => 'Atendimento em andamento'
);

$class = '';

switch ($object->getStatus()) {
    case TicketPeer::STATUS_FINALIZADO:
        $class = 'success';
        break;
    case TicketPeer::STATUS_PENDENTE:
        $class = 'danger';
        break;
    case TicketPeer::STATUS_EM_ANDAMENTO:
        $class = 'warning';
        break;
    default:
        $class = 'gray';
        break;
}

$form->configure(array(
    'class' => '',
    'action' => $request->server->get('REQUEST_URI'),
    "view" => new View\Vertical(),
));

$form->addElement(new Element\Hidden('ASSUNTO', $object->getAssunto(), array("id" => "assunto")));
$form->addElement(new Element\Hidden('TICKET_ID', $object->getId(), array("id" => "ticketSelecionado")));
$form->addElement(new Element\Hidden('USUARIO_NOME', $usuario->getNome(), array("id" => "usuarioNome")));
$form->addElement(new Element\Hidden('EMAIL_CLIENTE', $clienteEmail, array("id" => "emailCLiente")));

?>

<script src="<?= asset('/js/libs/summernote/summernote-lite.min.js') ?>"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

<div class="row">
    <div class="col-sm-6">
        <div class="panel panel-gray">
            <div class="panel-heading">Registro do ticket n° #<?= $_GET['id'] ?></div>
            <div class="panel-body" style="overflow: auto; height: 700px">
                <div class="messagesMain">
                                    
                </div>
                <?php $form->render(); ?>

                <div class="hidden-sm hidden-lg" <?= $object->getStatus() == 'FINALIZADO' ? 'hidden' : ''?>>
                    <textarea class="textareaXS form-control" id="novaMensagem" name="novaMensagem" rows="1"></textarea><br>
                    <button class="btn btn-success pull-right btnSendMessage" type="button">Enviar</button>
                </div>

                <div class="hidden-xs" <?= $object->getStatus() == 'FINALIZADO' ? 'hidden' : ''?>>
                    <textarea class="textarea form-control" id="novaMensagemXS" name="novaMensagem" rows="1"></textarea><br>
                    <button class="btn btn-success pull-right btnSendMessage" type="button">Enviar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="panel panel-<?= $class ?>">
            <div class="panel-heading">
                <h4>Status do ticket</h4>
            </div>
            <div class="panel-body">
                <div class="col-xs-12 col-sm-6">
                    <h3>Status Atual</h3>
                    <h3><span data-toggle="tooltip" data-placement="bottom" title="<?php echo $statusDescricao[$object->getStatus()] ?>"><?php echo $object->getStatusLabel() ?></span></h3>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <p>Você pode altera o staus deste ticket clicando na ação desejada abaixo:</p>
                    <div class="clearfix"></div>
                    
                    <a  class="statusClient btn btn-success reprove" 
                        data-toggle="tooltip" 
                        data-placement="bottom"
                        <?= $object->getStatus() == 'FINALIZADO' ? 'disabled' : ''?>
                        title="" 
                        href="<?php echo  get_url_admin() . '/ticket/status/?status=FINALIZADO&id='. $object->getId() ?>" 
                        data-original-title="Efetua a finalização do ticket">
                            <span class="icon-ok"></span> Finalizar
                    </a>

                    <a  class="statusClient btn btn-warning" 
                        data-toggle="tooltip" 
                        data-placement="bottom" 
                        <?= $object->getStatus() == 'FINALIZADO' ? 'disabled' : ''?>
                        title="" 
                        href="<?php echo  get_url_admin() . '/ticket/status/?status=EMANDAMENTO&id='. $object->getId() ?>" 
                        data-original-title="Sinaliza que o ticket está em andamento">
                            <span class="icon-time"></span> Em Andamento
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

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
        }   
        var ticketId = $('#ticketSelecionado').val();

        openModalMessages(ticketId);

        $('.btnSendMessage').on('click', function() {
            const ticketId = $('#ticketSelecionado').val();
            const mensagem = $('#novaMensagem').val() !== '' ?  $('#novaMensagem').val() : $('#novaMensagemXS').val();
            const assunto = $('#assunto').val();
            const remetente = 'ADMIN';
            const remetenteNome = $('#usuarioNome').val();
            const emailDestino = $('#emailCLiente').val();

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
                    if(response.status === 'ok') {
                        $('.note-editable').text('');
                        $('#novaMensagem').val();
                        openModalMessages(ticketId);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        })
    })   
</script>