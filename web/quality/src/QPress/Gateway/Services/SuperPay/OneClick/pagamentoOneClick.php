<?php
error_reporting(E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
/**
 * Exemplo de integração ao Gateway de pagamento
 * Método: Cadastro de Pagamento One Click
 * Autor: Bryan Marvila
 */

$dados_envio['codigoEstabelecimento'] = 1373891021810;
$dados_envio['IP'] = '192.165.154.198';
$dados_envio['idioma'] = '1';
$dados_envio['token'] = "1381262976557fc53f23f-bbae-458d-bb9e-2d04fa52c12a";
$dados_envio['numeroTransacao'] = 321;
$dados_envio['origemTransacao'] = '1';
$dados_envio['parcelas'] = '1';
$dados_envio['valor'] = 1005;
$dados_envio['itensDoPedido']['codigoCategoria'] = 321;
$dados_envio['itensDoPedido']['codigoProduto'] = 15;
$dados_envio['itensDoPedido']['nomeCategoria'] = 'teste';
$dados_envio['itensDoPedido']['nomeProduto'] = 'produto teste';
$dados_envio['itensDoPedido']['quantidadeProduto'] = 2;
$dados_envio['itensDoPedido']['valorUnitarioProduto'] = 1005;
/*
 * Criação do objeto responsável por transformar
 * o array criado em um xml
 * Biblioteca usada NuSoap
 */	
include 'model/soapModel.php';
$soap = new soapModel();
$soap = $soap->pagamentoOneClick($dados_envio);
// Exemplo do retorno obtido
/*
 * 
$soap[return] => Array
        (
            [autorizacao] => 0
            [codigoEstabelecimento] => 1373891021810
            [codigoFormaPagamento] => 120
            [codigoTransacaoOperadora] => 0
            [dataAprovacaoOperadora] => 
            [mensagemVenda] => 
            [numeroComprovanteVenda] => 
            [numeroTransacao] => 321
            [parcelas] => 1
            [statusTransacao] => 0
            [taxaEmbarque] => 0
            [urlPagamento] => http://homologacao2.superpay.com.br/superpay/?cod=1381338546333b4eb27f7-b4fa-4008-96b0-9cf3dca303cf
            [valor] => 1005
            [valorDesconto] => 0
        )
*/