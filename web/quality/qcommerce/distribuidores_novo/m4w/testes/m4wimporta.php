<?php

    $listas = array('Lista Distribuidores', 'Lista Distribuidores 1');

    $contatos = array(
        array('nome' => 'Wallace',
            'email' => 'wperini@gmail.com'),
        array('nome' => 'João da Silva',
            'email' => 'joaosilva@gmail.com'));


    $contatos = json_encode($contatos);
    $listas   = json_encode($listas);

    $host = 'https://mail4web.com.br'; //host do mail4web

    $funcao = '/api/contatos/importa'; //funcao da API
    $apikey = 'x2kupjqly2wx3rp4ouj34l26ukofbsfn4nfo7qjhgav6vw6mps'; //chave de API do usuario

    ?>
<form action="<?= "$host$funcao?apikey=$apikey"; ?>" method="post">

    <input type="hidden" name="contatos" value='<?= $contatos; ?>'>
    <input type="hidden" name="listas" value='<?= $listas; ?>'>

    <button type="submit" name="enviar">Enviar Contatos</button>

</form>
