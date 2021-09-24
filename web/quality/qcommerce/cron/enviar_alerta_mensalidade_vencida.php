<?php

/*
 * Script que envia um e-mail de alerta ao cliente quando a mensalidade dele venceu há 01 dia.
 */

set_time_limit(0);
ini_set('memory_limit', '-1');

$data = new DateTime('-1 day');

$query = ClienteQuery::create()
            ->filterByLivreMensalidade(false)
            ->where("DATE_FORMAT(Cliente.vencimentoMensalidade, '%Y-%m-%d') = ?", $data->format('Y-m-d'), PDO::PARAM_STR);

foreach ($query->find() as $cliente) { /* @var $cliente Cliente */
    try {
        QPress\Mailing\Mailing::enviarAlertaMensalidadeVencida($cliente);
    } catch (Exception $ex) {
        error_log($ex->getMessage());
    }
}
