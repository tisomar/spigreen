<?php

$con = Propel::getConnection();

$distribuicao = ExtratoBonusProdutosQuery::create()
    ->filterByClienteId($request->request->get('id'))
    ->filterByDistribuicaoId($request->request->get('distribuicaoid'))
    ->findOne();

if ($distribuicao) :

    if (!$distribuicao->getIsDistribuido()) :
        $distribuicao->setDataRetirada(new DateTime());
        $distribuicao->setIsDistribuido(true);
        $distribuicao->save();
        
        $participacaoResultadoCliente = new ExtratoBonusProdutos();
        $participacaoResultadoCliente->setClienteId($request->request->get('id'));
        $participacaoResultadoCliente->setProdutosBonusId($distribuicao->getProdutosBonusId());
        $participacaoResultadoCliente->setDistribuicaoId($request->request->get('distribuicaoid'));
        $participacaoResultadoCliente->setPlanoCarreiraId($distribuicao->getPlanoCarreiraId());
        $participacaoResultadoCliente->setData(new DateTime());
        $participacaoResultadoCliente->setDataRetirada(new DateTime());
        $participacaoResultadoCliente->setValorTotalBonificacao($distribuicao->getValorTotalBonificacao());
        $participacaoResultadoCliente->setOperacao('-');
        $participacaoResultadoCliente->setIsDistribuido(true);
        $participacaoResultadoCliente->setObservacao('Bônus produtos encaminhado ao cliente.');
        $participacaoResultadoCliente->save($con);
        
        $checkOperacaoClientes = ExtratoBonusProdutosQuery::create()
        ->filterByDataRetirada(null, Criteria::ISNULL)
        ->filterByDistribuicaoId($request->request->get('distribuicaoid'))
        ->find()
        ->count();

        if($checkOperacaoClientes >= 1) :
            echo json_encode(['status' => 'success', 'message' => 'Distribuição confirmada com sucesso.']);
        else:
            $distribuicao = DistribuicaoBonusProdutosQuery::create()
                ->filterById($request->request->get('distribuicaoid'))
                ->filterByStatus(Distribuicao::STATUS_PREVIEW)
                ->findOne();
    
            if( $distribuicao ) :
                $distribuicao->setStatus(Distribuicao::STATUS_DISTRIBUIDO);
                $distribuicao->save();
            endif;
    
            echo json_encode(['status' => 'success', 'message' => 'Distribuição confirmada com sucesso.']);
        endif;
    endif;
else:
    echo json_encode(['status' => 'error', 'message' => 'Distribuição não encontrada.']);
endif;
exit;




