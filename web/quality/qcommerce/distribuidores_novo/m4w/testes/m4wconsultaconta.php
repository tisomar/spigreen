<?php

$host = 'https://mail4web.com.br'; //host do mail4web

$funcao = '/api/utilizacao_conta'; //funcao da API
$apikey = 'x2kupjqly2wx3rp4ouj34l26ukofbsfn4nfo7qjhgav6vw6mps'; //chave de API do usuario

$contents = file_get_contents("$host$funcao?apikey=$apikey");
if ($contents !== false) {
    $contentsLogin = json_decode($contents);
    if ($contentsLogin !== null) {
        var_dump($contentsLogin);
    }
}
