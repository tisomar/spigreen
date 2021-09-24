<?php
/*
 * Este script será invocado quando o usuário confimar o pagamento na pagina do PayPal.
 * Nesta tela podemos exibir ao cliente uma tela de confirmação final ou solicitar que o PayPal conclua o checkout diretamente (o que é feito agora).
 * Depois de solicitar ao PayPal o checkout vamos redirecionar o cliente para a tela de sucesso.
 */
$strIncludesKey = '';

require_once __DIR__ . '/../includes/security.php';

include_once __DIR__ . '/actions/retorno.paypal.actions.php';
