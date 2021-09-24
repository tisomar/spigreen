<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 19/06/2018
 * Time: 11:56
 */

set_time_limit(0);
ini_set('memory_limit', '-1');

$date = DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
$date->sub(new DateInterval('P1D'));

$queryClientesZerar = ClienteQuery::create()
                        ->where('cast(qp1_pre_cadastro_cliente.DATA_FINALIZACAO as date) <= "' . $date->format('Y-m-d') . '"')
                        ->usePreCadastroClienteQuery()
                            ->filterByConcluido(0, Criteria::EQUAL)
                        ->endUse();

$arrClientesZerar = $queryClientesZerar->find();

if (count($arrClientesZerar) > 0) {
    $countParameter = Config::get('sistema.cadastro_vago');
    $count = 1;
    Config::saveParameter('sistema.cadastro_vago', count($arrClientesZerar) + $countParameter);

    foreach ($arrClientesZerar as $objClienteZerar) {
        /** @var $objClienteZerar Cliente */
        $retorno = $objClienteZerar->zerarCadastro($countParameter + $count);
        echo 'Cliente ' . $objClienteZerar->getNomeCompleto() . ' - ' . $retorno;
        $count++;
    }
}
