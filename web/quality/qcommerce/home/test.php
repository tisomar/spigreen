<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 26/04/2018
 * Time: 14:59
 */

/* @var $container \QPress\Container\Container */

set_time_limit(0);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

ini_set('memory_limit', '1024M');
error_reporting(E_ALL);

$base = ClienteQuery::create()->findRoot();

$query = ClienteQuery::create()
    //sem filhos
    ->where('(qp1_cliente.TREE_RIGHT - qp1_cliente.TREE_LEFT) = 1 ')

    //descendentes de base
    ->where('qp1_cliente.TREE_LEFT > ' . $base->getTreeLeft())
    ->where('qp1_cliente.TREE_RIGHT < ' . $base->getTreeRight())

    ->where('qp1_cliente.INDICADOR_ID is not null')
    ->where('qp1_cliente.Id NOT IN 
                        (SELECT INDICADOR_ID FROM qp1_cliente WHERE INDICADOR_ID is not null group by INDICADOR_ID)')

    ->orderBy('qp1_cliente.TREE_LEVEL', Criteria::DESC);
var_dump($query->toString());
die;
//$gateway->initialize($aditionalParameters);
