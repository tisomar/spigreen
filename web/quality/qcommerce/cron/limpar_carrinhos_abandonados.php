<?php

/**
 * Esta rotina remove os carrinhos criados que não possuem itens ou que não
 * possuem cliente declarado.
 *
 * Esta rotina pode ser executada 1x ao dia entre às 3h. - * 0 3 * * * /path_to_script
 * 0 3 * * * /path_to_script
 */

include __DIR__ . '/../includes/include_config.inc.php';

$coll = CarrinhoQuery::create()
        ->filterByClassKey(2)
        ->filterByValorItens(0)
        ->_or()
        ->filterByClienteId(null)
        ->find()
    ->delete();
