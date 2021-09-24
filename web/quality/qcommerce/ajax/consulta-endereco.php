<?php
$cep = preg_replace('/[^0-9]/', '', $_GET['cep']);
$address = \QPress\Correios\CorreiosEndereco::consultaCepViaCep($cep);
header('Content-Type: text/json');
echo json_encode($address);
exit;
