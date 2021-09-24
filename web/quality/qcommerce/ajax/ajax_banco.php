<?php

$banco = null;

if ($request->request->has('idBanco')):
    $idBanco = $request->request->get('idBanco');

    $banco = BancoCadastroClienteQuery::create()
        ->filterById($idBanco)
        ->findOne()
        ->toJSON();
endif;

echo $banco;
