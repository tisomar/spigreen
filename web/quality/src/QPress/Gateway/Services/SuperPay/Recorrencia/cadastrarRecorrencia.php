<?php
error_reporting(E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
/**
 * Exemplo de integra��o ao Gateway de pagamento
 * M�todo: Cadastrar Recorr�ncia
 * Autor: Bryan Marvila
 */

$telefonesDadosCobranca = array(
		'ddi'=>"55",
		'ddd'=>"21",
		'telefone'=>"65486548",
		'tipoTelefone'=>1
);
$telefonesDadosEntrega = array(
		'ddi'=>"55",
		'ddd'=>"21",
		'telefone'=>"98746543",
		'tipoTelefone'=>1
);

//Campo deve ser alterado nos Defines no inicio do arquivo
$dados_envio['quantidadeCobrancas'] = 	5;
// Verificar lista de periodicidade no Manual
$dados_envio['periodicidade'] = 		3;
$dados_envio['diaCobranca'] = 5;
$dados_envio['mesCobranca'] = 10;
$dados_envio['primeiraCobranca'] = 	2;
//Verificar manual para cobran�a imediata
$dados_envio['processarImediatamente'] = 2;
$dados_envio['dadosCobranca']['paisComprador']= "Brasil";
$dados_envio['dadosEntrega']['paisEntrega']= "Brasil";
//TelefoneComprador(Lista-Array)
$dados_envio['dadosCobranca']['telefone']= $telefonesDadosCobranca;
//TelefoneComprador(Lista-Array)
$dados_envio['dadosEntrega']['telefone']= $telefonesDadosEntrega;
// Seu c�digo de estabelecimento junto ao Gateway
$dados_envio["estabelecimento"] = 1373891021810;
// c�digo �nico que identificar� o pedido em sua base
$dados_envio['numeroRecorrencia'] = 6516817;
//valor com a formatacao de 100 para transacoes com R$ 1,00, por exemplo
$dados_envio["valor"] = 1005; // R$ 10,05
//C�digo da forma de pagamento, a lista destes c�digo � encontrada no Manual de Integra��o
$dados_envio["codigoFormaPagamento"] = 120;
/*
 * urlNotificacao: este � o endere�o chamado quando h� a altera��o de
* status de um pedido enviando para ele o codigoEstabelecimento e numeroTransacao
*/
$dados_envio["urlNotificacao"] = "dominio/caminho/para_para_o_metodo/campainha";
$dados_envio['dadosCartao']['nomePortador'] = "Manoel Moreira";
$dados_envio['dadosCartao']['numeroCartao'] = "5555666677778884";
$dados_envio['dadosCartao']['codigoSeguranca'] = "654";
$dados_envio['dadosCartao']['dataValidade'] = "12/2012";
$dados_envio['dadosCobranca']['nomeComprador']=	"Manoel Moreira";
$dados_envio['dadosCobranca']['emailComprador']= "email@dominio.com.br";
$dados_envio['dadosCobranca']['enderecoComprador']=	"Ant�nio Francisco Lisboa";
$dados_envio['dadosCobranca']['bairroComprador']= "Vald�bia";
$dados_envio['dadosCobranca']['complementoComprador']=	"casa";
$dados_envio['dadosCobranca']['cidadeComprador']= "S�o Bernardo do Campo";
$dados_envio['dadosCobranca']['estadoComprador']= "S�o Paulo";
$dados_envio['dadosCobranca']['cepComprador']= "09820120";
$dados_envio['dadosEntrega']['nomeEntrega']= "Manoel Moreira";
$dados_envio['dadosEntrega']['emailEntrega']= "email@dominio.com.br";
$dados_envio['dadosEntrega']['enderecoEntrega']= "Ant�nio Francisco Lisboa";
$dados_envio['dadosEntrega']['bairroEntrega']= "Vald�bia";
$dados_envio['dadosEntrega']['complementoEntrega']=	"casa";
$dados_envio['dadosEntrega']['cidadeEntrega']= "S�o Bernardo do Campo";
$dados_envio['dadosEntrega']['estadoEntrega']= "S�o Paulo";
$dados_envio['dadosEntrega']['cepEntrega']= "09820120";
/*
 * Cria��o do objeto respons�vel por transformar
 * o array criado em um xml
 * Biblioteca usada NuSoap
 */	
include 'model/soapModel.php';
$soap = new soapModel();
$soap = $soap->criarRecorrencia($dados_envio);
// Exemplo do retorno obtido
/*
 * 
$soap(
	[return] => Array(
		[estabelecimento] => 1355835042461
		[numeroRecorrencia] => 538
		[status] => true
		[valor] => 1005
	)
)
*/