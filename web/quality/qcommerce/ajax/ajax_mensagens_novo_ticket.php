<?php

$ticketId = !empty($_POST['ticketId']) ? $_POST['ticketId'] : 0;
$novaMensagem  = !empty($_POST['mensagem']) ? $_POST['mensagem'] : 0;
$assunto  = !empty($_POST['assunto']) ? $_POST['assunto'] : '';
$emailDestino  = !empty($_POST['emailDestino']) ? $_POST['emailDestino'] : '';
$remetente  = !empty($_POST['remetente']) ? $_POST['remetente'] : 'ADMIN';
$remetenteNome = $_POST['remetenteNome'] ?? 'ADMIN';

$response = ['status' => 'erro'];

if($ticketId !== 0 && $novaMensagem !== 0) :

   $ticketMensagem = new TicketMessages();
   $ticketMensagem->setTicketId($ticketId);
   $ticketMensagem->setRemetente($remetente);
   $ticketMensagem->setRemetenteNome($remetenteNome);
   $ticketMensagem->setMensagem($novaMensagem);
   $ticketMensagem->setData(date('Y-m-d H:i:s'));
   $ticketMensagem->save();

   \QPress\Mailing\Mailing::send($emailDestino, $assunto, $novaMensagem);
   $response = ['status' => 'ok'];
endif;

echo json_encode($response);

