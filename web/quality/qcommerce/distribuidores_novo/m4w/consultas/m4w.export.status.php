<?php
/**
 * Created by PhpStorm.
 * User: perini
 * Date: 28/01/2017
 * Time: 09:18
 */


$host = 'https://mail4web.com.br'; //host do mail4web

$funcao = '/api/contatos/status_importacao/{id}'; //funcao da API
$idImportacao = $_GET['id']; //Id da importação que será consultada
$funcao = str_replace('{id}', $idImportacao, $funcao); //substitui parametro

$apikey = $objConfiguracao->getChaveApiMailforweb(); //chave de API do usuario

$contents = file_get_contents("$host$funcao?apikey=$apikey");
if ($contents !== false) {
    $statusImportacao = json_decode($contents);
    if ($statusImportacao !== null) {
        $status = $statusImportacao->status;
        $url = $statusImportacao->url_detalhes;
    }
}
