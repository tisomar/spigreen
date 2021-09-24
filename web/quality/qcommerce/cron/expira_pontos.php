<?php

/*
 * Script que verifica os pontos dos clientes que expiraram e lança os pontos expirados como negativo no extrato.
 */

set_time_limit(0);
ini_set('memory_limit', '-1');

Propel::disableInstancePooling();

$gerenciador = new GerenciadorPontos($con = Propel::getConnection(), $logger);

$query = ClienteQuery::create()->orderById();

foreach ($query->find() as $cliente) {
    try {
        $gerenciador->expiraPontosCliente($cliente);
    } catch (Exception $ex) {
        if ($ex instanceof PropelException || $ex instanceof PDOException) {
            throw $ex; //se for exceção do banco de dados, deixa o script encerrar.
        }
        echo "Erro ao expirar os pontos do cliente {$cliente->getId()}.<br>";
        error_log($ex->getMessage());
    }
}

echo 'Finalizado com sucesso.';
