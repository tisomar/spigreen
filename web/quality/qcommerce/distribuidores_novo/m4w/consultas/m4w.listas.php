<?php

if ($objConfiguracao->getChaveApiMailforweb() <> '') {
    $host = 'https://mail4web.com.br'; //host do mail4web

    $funcao = '/api/listas'; //funcao da API
    $apikey = $objConfiguracao->getChaveApiMailforweb(); //chave de API do usuario

    $contents = file_get_contents("$host$funcao?apikey=$apikey");

    if ($contents !== false) {
        $objListas = json_decode($contents);
    }
}
