<?php
if (isset($_POST['dataInicial'])) {
    $dataInicial = DateTime::createFromFormat('d/m/Y', $_POST['dataInicial']);
} else {
    $dataInicial = DateTime::createFromFormat('d/m/Y', date('01/m/Y'));
}
    $dataInicial->setTime(0, 0, 0);

if (isset($_POST['dataFinal'])) {
        $dataFinal = DateTime::createFromFormat('d/m/Y', $_POST['dataFinal']);
} else {
    $dataFinal = DateTime::createFromFormat('d/m/Y', date('t/m/Y'));
}
    $dataInicial->setTime(23, 59, 59);
    
    $atividades = $query = DistribuidorEventoQuery::create()
        ->filterByData($dataInicial, Criteria::GREATER_EQUAL)
        ->filterByData($dataFinal, Criteria::LESS_EQUAL)
        ->filterByCliente(ClientePeer::getClienteLogado())
        ->find();
    $assuntos = DistribuidorEventoPeer::getSubjects();
    
    $atividadesAbertas = array();
    $atividadesConcluidas = array();
    
    $atividadesTotal = 0;
    $valorTotal = 0;
    
    $atividadesGanho = 0;
    $valorGanho = 0;
    
    $atividadesPerda = 0;
    $valorPerdido = 0;
    
    $maiorAtividadeAberta = 0;
    $maiorAtividadeConcluida = 0;
    
    $motivosPerda = array();
    $maiorMotivoPerda = 0;

/* @var $atividade DistribuidorEvento */
foreach ($atividades as $atividade) {
    $atividadesTotal++;
    $valorTotal += $atividade->getValor();
    if ($atividade->getStatus() == DistribuidorEvento::STATUS_ANDAMENTO) {
        if (array_key_exists($atividade->getAssunto(), $atividadesAbertas)) {
            $atividadesAbertas[$atividade->getAssunto()]++;
        } else {
            $atividadesAbertas[$atividade->getAssunto()] = 1;
        }

        if ($maiorAtividadeAberta < $atividadesAbertas[$atividade->getAssunto()]) {
            $maiorAtividadeAberta = $atividadesAbertas[$atividade->getAssunto()];
        }
    } else {
        if (array_key_exists($atividade->getAssunto(), $atividadesConcluidas)) {
            $atividadesConcluidas[$atividade->getAssunto()]++;
        } else {
            $atividadesConcluidas[$atividade->getAssunto()] = 1;
        }


        if ($maiorAtividadeConcluida < $atividadesConcluidas[$atividade->getAssunto()]) {
            $maiorAtividadeConcluida = $atividadesConcluidas[$atividade->getAssunto()];
        }

        if ($atividade->getDistribuidorTemplateIdPerda()) {
            $atividadesPerda++;

            $valorPerdido += $atividade->getValor();

            /* @var $template DistribuidorTemplate */
            $template = DistribuidorTemplateQuery::create()->findPk($atividade->getDistribuidorTemplateIdPerda());
                
            $assunto = $template != null ? $template->getAssunto() : 'Outros';

//                var_dump($assunto);die;

            if (array_key_exists($assunto, $motivosPerda)) {
                $motivosPerda[$assunto]++;
            }
            $motivosPerda[$assunto] = 1;


            if ($maiorMotivoPerda < $motivosPerda[$assunto]) {
                $maiorMotivoPerda = $motivosPerda[$assunto];
            }
        } else {
            $atividadesGanho++;
            $valorGanho += $atividade->getValor();
        }
    }
}

$produtos = ProdutoQuery::create()
        ->withColumn('(SELECT COUNT((SELECT CLIENTE_ID FROM qp1_distribuidor_evento WHERE ati_pro.DISTRIBUIDOR_EVENTO_ID = qp1_distribuidor_evento.ID AND CLIENTE_ID = ' . ClientePeer::getClienteLogado()->getId() . ')) FROM qp1_distribuidor_evento_produto ati_pro WHERE ati_pro.PRODUTO_ID = qp1_produto.ID)', 'tem_compras')
        ->addHaving('tem_compras', 1, Criteria::GREATER_EQUAL)
        ->orderBy('tem_compras', Criteria::DESC)
        ->find();

    $maiorCompra = 0;
if (count($produtos) > 0 && $produtos[0] instanceof Produto) {
    $maiorCompra = $produtos[0]->getVirtualColumn('tem_compras');
}
