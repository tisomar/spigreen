<?php
error_reporting(E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
/**
 * Exemplo de integração ao Gateway de pagamento
 * Método: Cadastro de Pagamento One Click
 * Autor: Bryan Marvila
 */

$dados_envio["codigoEstabelecimento"] = 1373891021810;
$dados_envio['nomeTitularCartaoCredito'] = "Manoel Moreira";
$dados_envio['numeroCartaoCredito'] = "5555666677778884";
$dados_envio['codigoSeguranca'] = "654";
$dados_envio['dataValidadeCartao'] = "12/2012";
$dados_envio['emailComprador']= "email@dominio.com.br";
$dados_envio["formaPagamento"] = 120;
/*
 * Criação do objeto responsável por transformar
 * o array criado em um xml
 * Biblioteca usada NuSoap
 */	
include 'model/soapModel.php';
$soap = new soapModel();
$soap = $soap->cadastraPagamentoOneClick($dados_envio);
// Exemplo do retorno obtido
/*
 * 
$soap(
	[return] => 1381262976557fc53f23f-bbae-458d-bb9e-2d04fa52c12a
)
*/