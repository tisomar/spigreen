<?php

$host = 'https://mail4web.com.br'; //host do mail4web

$funcao = '/api/utilizacao_conta'; //funcao da API
$apikey = $objConfiguracao->getChaveApiMailforweb(); //chave de API do usuario

$contents = file_get_contents("$host$funcao?apikey=$apikey");
if ($contents !== false) {
    $objLogin = json_decode($contents);
    if ($obj !== null) {
        var_dump($contentsLogin);
    }
}
