<?php

//suporte deve ser exibido apenas para clientes com plano ativo.
//if (!ClientePeer::getClienteLogado(true)->getPlanoId()) {
//    redirectTo(get_url_site() . '/minha-conta/pedidos');
//    exit;
//}

$textoIntrodutorio = '';
if ($conteudo = ConteudoPeer::get('suporte_introducao')) {
    $textoIntrodutorio = trim($conteudo->getDescricao());
}


$query = SuporteQuery::create()
                ->filterByMostrar(true)
                ->addDescendingOrderByColumn("qp1_suporte.TIPO = 'VIDEO_AULA'") /* exibe a categoria video aula primeiro */
                ->orderByOrdem()
                ->orderById();

$suportes = array();
foreach ($query->find() as $suporte) {
    if($suporte->getTipo() == Suporte::TIPO_ARQUIVO && empty($suporte->getArquivo())) :
        continue;
    endif;
    $suportes[$suporte->getTipo()][] = $suporte;
}
