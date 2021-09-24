<?php

$inicio = time();

$gerenciador = new GerenciadorBonusUnilevel(Propel::getConnection(), $logger);

$cliente = ClientePeer::retrieveByPK(277);

$planoCarreira = $cliente->getPlanoCarreira(11, 2020)->getPlanoCarreira();

$perc = $planoCarreira->getPercBonusLideranca();

var_dump($gerenciador->getValorLiderancaCliente($cliente, $perc, $perc, 11, 2020));

echo '<br>Tempo: ', number_format(time() - $inicio, 0, '', '.'), ' (s)';
