<?php

$clientePai = 132;
$clienteFilho = 141;

$objClientePai = ClientePeer::retrieveByPK($clientePai);
$objClienteFilho = ClientePeer::retrieveByPK($clienteFilho);

$objClienteFilho->moveToFirstChildOf($objClientePai);
$objClienteFilho->save();
