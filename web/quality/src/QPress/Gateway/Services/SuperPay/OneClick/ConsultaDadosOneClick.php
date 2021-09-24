<?php
error_reporting(E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
/**
 * Exemplo de integração ao Gateway de pagamento
 * Método: Cadastro de Pagamento One Click
 * Autor: Bryan Marvila
 */

$token = "1381262976557fc53f23f-bbae-458d-bb9e-2d04fa52c12a";
/*
 * Criação do objeto responsável por transformar
 * o array criado em um xml
 * Biblioteca usada NuSoap
 */	
include 'model/soapModel.php';
$soap = new soapModel();
$soap = $soap->consultaDadosOneClick($token);
// Exemplo do retorno obtido
/*
 * 
$soap[return] => Array
	(
		[codigoEstabelecimento] => 1373891021810
        [codigoSeguranca] => 
        [dataValidadeCartao] => 12/2012
        [emailComprador] => email@dominio.com.br
        [formaPagamento] => 120
        [numeroCartaoCredito] => 555566******8884
    )
*/