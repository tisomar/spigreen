<?php

/*
$contatos = array(
    array('nome' => 'Wallace',
        'email' => 'wperini@gmail.com'),
    array('nome' => 'João da Silva',
        'email' => 'ssjoaosilva@gmail.com'));

$contatos = json_encode($contatos);
*/

    //$_POST['listas'] = json_encode(array('Lista Distribuidores'));

    $listas   = json_decode($_POST['listas']);
    $contatos = json_decode($_POST['contatos']);

foreach ($listas as $b) {
    $elementob = $b;
}

foreach ($contatos as $a) {
    $elemento = $a->{'nome'} ;
}
?>
{
    "resultado":"ok",
    "id_importacao": "6176",
    "url_api_consulta": "https:\/\/mail4web.com.br\/api\/contatos\/status_importacao\/6176?apikey=x2kupjqly2wx3rp4ouj34l26ukofbsfn4nfo7qjhgav6vw6mps","url_detalhes":"https:\/\/mail4web.com.br\/system\/contacts\/import\/6176\/report"
}
