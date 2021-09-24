<?php
error_reporting(E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
/**
 * Exemplo de integra��o ao Gateway de pagamento
 * M�todo: consultaTransacaoRecorrenciaWS
 * Autor: Bryan Marvila
 */

// Seu c�digo de estabelecimento junto ao Gateway
$dados_envio["estabelecimento"] = 1373891021810;
// c�digo �nico que identificar� o pedido em sua base
$dados_envio['numeroRecorrencia'] = 6516816;

/*
 * Cria��o do objeto respons�vel por transformar
 * o array criado em um xml
 * Biblioteca usada NuSoap
 */	
include 'model/soapModel.php';
$soap = new soapModel();
$soap = $soap->cancelarRecorrencia($dados_envio);
// Exemplo do retorno obtido
/*
 * 
$soap(
	[return] => Array(
		[estabelecimento] => 1355835042461
		[numeroRecorrencia] => 538
		[status] => false
		[valor] => 1005
	)
)
*/