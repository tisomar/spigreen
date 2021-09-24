<?php
/*
 * Este script será invocado quando o usuário cancelar o pagamento na pagina do PayPal.
 */

$strIncludesKey = '';

require_once __DIR__ . '/../includes/security.php';

include_once __DIR__ . '/actions/cancelamento.paypal.actions.php';
