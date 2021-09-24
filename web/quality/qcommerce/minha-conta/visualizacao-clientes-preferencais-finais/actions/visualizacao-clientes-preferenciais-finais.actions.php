<?php
$clienteLogado = ClientePeer::getClienteLogado(true);

$filtroNome = $request->query->get('cliente');
$filtroTipoCliente = $request->query->get('tipo_cliente');

try {
    $query = ClienteQuery::create()
        ->leftJoin('Plano')
        ->select(['Id', 'Nome', 'Tipo', 'Email', 'Nivel'])
        ->withColumn('Cliente.Nome', 'Nome')
        ->withColumn('CASE WHEN Plano.Id IS NULL THEN "Final" WHEN Plano.PlanoClientePreferencial = 1 THEN "Preferencial" END', 'Tipo')
        ->withColumn('Cliente.Email', 'Email')
        ->withColumn('Cliente.TreeLevel', 'Nivel')
        ->condition('condTree1', 'Cliente.TreeLeft > ?', $clienteLogado->getTreeLeft())
        ->condition('condTree2', 'Cliente.TreeRight < ?', $clienteLogado->getTreeRight())
        ->combine(['condTree1', 'condTree2'], 'and', 'combineTree')
        ->condition('condPlano1', 'Plano.Id is null')
        ->condition('condPlano2', 'Plano.PlanoClientePreferencial = ?', 1)
        ->combine(['condPlano1', 'condPlano2'], 'or', 'combinePlano')
        ->condition('condVago', 'Cliente.Vago <> ?', 1)
        ->where(['combinePlano', 'combineTree', 'condVago'], 'and')
        ->orderBy('Cliente.Nome', Criteria::ASC);

    if ($filtroNome) :
        $query->where('Cliente.Nome LIKE ?', '%'.$request->query->get('cliente').'%');
    endif;

    if ($filtroTipoCliente) :
        if ($filtroTipoCliente == 'final') :
            $query->where('Plano.Id is null');
        elseif ($filtroTipoCliente == 'preferencial') :
            $query->where('Plano.PlanoClientePreferencial = ?', 1);
        endif;
    endif;

    $page = (int)$router->getArgument(0);

    if ($page < 1) :
        $page = 1;
    endif;

    $pager = $query->paginate($page, 10);

    $queryString = '';

    if ($qs = $request->getQueryString()) :
        $queryString = '?' . $qs;
    endif;
} catch (\PropelException $e) {
    $logger->error($e->getMessage());
}
