<?php

$estadoId = $request->query->get('estado_id', 0);

$cidades = CidadeQuery::create()
    ->filterByEstadoId($estadoId)
    ->orderByNome()
    ->find();

foreach ($cidades as $cidade) : /* @var $cidade Cidade */
    printf(
        '<option value="%d">%s</option>',
        $cidade->getId(),
        $cidade->getNome()
    );
endforeach;
