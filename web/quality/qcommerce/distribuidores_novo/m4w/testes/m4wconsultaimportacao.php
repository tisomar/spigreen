<?php
/**
 * Created by PhpStorm.
 * User: perini
 * Date: 28/01/2017
 * Time: 09:18
 */


$host = 'https://mail4web.com.br'; //host do mail4web

$funcao = '/api/contatos/status_importacao/{id}'; //funcao da API
$idImportacao = 6176; //Id da importação que será consultada
$funcao = str_replace('{id}', $idImportacao, $funcao); //substitui parametro

$apikey = 'x2kupjqly2wx3rp4ouj34l26ukofbsfn4nfo7qjhgav6vw6mps'; //chave de API do usuario

$contents = file_get_contents("$host$funcao?apikey=$apikey");
if ($contents !== false) {
    $statusImportacao = json_decode($contents);
    if ($statusImportacao !== null) {
        echo "Status importação $idImportacao: " . $statusImportacao->status;
        echo "<br>Total importados $idImportacao: " . $statusImportacao->total_importados;
        echo "<br>Nome Importação $idImportacao: " . $statusImportacao->nome;
        echo "<br>Data_cadastro $idImportacao: " . $statusImportacao->data_cadastro;
        echo "<br>Url Detalhes $idImportacao: " . $statusImportacao->url_detalhes;
    }
}
