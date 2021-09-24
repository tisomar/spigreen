<?php
 
include('../includes/config.inc.php');
include('../includes/security.inc.php');

$pedido = PedidoPeer::retrieveByPK($_GET['pedido']);
$pedi1_cod_ras = $_GET["id"];

$pedido->gravarCodigoRastreio($pedi1_cod_ras);
