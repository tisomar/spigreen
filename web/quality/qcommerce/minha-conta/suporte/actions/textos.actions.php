<?php

//suporte deve ser exibido apenas para clientes com plano ativo.
//if (!ClientePeer::getClienteLogado(true)->getPlanoId()) {
//    redirectTo(get_url_site() . '/minha-conta/pedidos');
//    exit;
//}

$suporte = SuporteQuery::create()
                ->filterByMostrar(true)
                ->filterByTipo(Suporte::TIPO_TEXTO)
                ->filterById($args[0])
                ->findOne();

if (!$suporte) {
    redirect_404();
}
