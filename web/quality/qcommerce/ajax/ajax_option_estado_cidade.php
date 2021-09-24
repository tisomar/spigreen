<?php

$estadoId = $_POST['estadoId'] ?? null;

echo '<option>Selecione o Centro de Distribuição</option>';

if (is_null($estadoId)) :
    die;
endif;

$retiradaLoja = RetiradaLojaQuery::create()
    ->filterByHabilitado(1)
    ->useCidadeQuery()
        ->filterByEstadoId($estadoId)
    ->endUse();

foreach ($retiradaLoja->find() as $obj) :
    printf(
        '<option value="%d">%s</option>',
        $obj->getId(),
        $obj->getNome()
    );
endforeach;
