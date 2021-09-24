<?php

$ticketId = !empty($_POST['ticketId']) ? $_POST['ticketId'] : 0;

$messagesHtml = '
   <style> 
      .boxMensagem {
         margin: 20px 0; 
         border: 1px solid #f9f9f9;
         padding: 10px; 
         border-radius: 5px;
      }

      .boxMensagem img{
         max-width: 100%;
      }

      .cliente {
         border: 1px solid #ddd;
         background: #fcfcfc; 

      }
      .admin {
         background: #efefef;  
         border: 1px solid #ccc;
      }
      .userInfo{
         display: flex;
         flex-direction: row;
         justify-content: space-between;
      }
      .mensagem{
         margin-top: 10px;
      }
   </style>
   <div class="messagesMain">';

if($ticketId !== 0) :
   $ticketMessages = TicketMessagesQuery::create()
      ->filterByTicketId($ticketId)
      ->orderByData(CRITERIA::ASC)
      ->find();

   foreach($ticketMessages as $messages) :
      $classMessage = $messages->getRemetente() === 'CLIENTE' ? 'cliente' : 'admin';

      $messagesHtml .= 
         "<div class='messagesBox {$classMessage} boxMensagem '>" .
            '<div class="userInfo">' .
               '<p>' . $messages->getRemetenteNome()  . '</p>' .
               '<p>' . $messages->getData('d/m/Y H:i') . '</p>' . 
            '</div>' .
            '<div class="mensagem">'. 
               '<p><strong>' . $messages->getMensagem() . '</strong></p>' .
            '</div>' .
         '</div>';
   endforeach;
endif;

$messagesHtml .= '</div>'; 

echo json_encode($messagesHtml);

