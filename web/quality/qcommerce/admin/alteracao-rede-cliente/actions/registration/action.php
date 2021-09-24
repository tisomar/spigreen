<?php

$query = ClienteQuery::create()
    ->select(['ID', 'NOME'])
    ->withColumn(ClientePeer::ID, 'ID')
    ->withColumn(
        sprintf(
            'IF(%s IS NOT NULL, %s, %s)',
            ClientePeer::CNPJ,
            ClientePeer::RAZAO_SOCIAL,
            ClientePeer::NOME
        ),
        'NOME'
    )
    ->join('Plano')
    ->addJoinCondition('Plano', 'Plano.PlanoClientePreferencial <> ?', '1')
    ->filterByVago(0, Criteria::EQUAL)
    ->find()
    ->toArray();

$listaClientes = [
    '' => ''
];

foreach ($query as $cliente) :
    $listaClientes[$cliente['ID']] = $cliente['NOME'];
endforeach;

if ($request->getMethod() == 'POST') :

    $clienteMover = $request->request->get('filter')['ClienteMoverId'];
    $clienteDestino = $request->request->get('filter')['ClienteDestinoId'];

    $msgErro = null;

    if ($clienteMover === '') :
        $msgErro = 'Informe o cliente que deseja mover!';
    endif;

    if ($clienteDestino === '') :
        $msgErro = 'Informe o cliente de destino!';
    endif;

    if ($clienteMover === $clienteDestino) :
        $msgErro = 'O cliente para mover não pode ser igual ao cliente destino!';
    endif;

    /** @var $clienteMover Cliente */
    $clienteMover = ClienteQuery::create()->filterById($clienteMover)->findOne();
    /** @var $clienteDestino Cliente */
    $clienteDestino = ClienteQuery::create()->filterById($clienteDestino)->findOne();

    if ($clienteDestino->getTreeLeft() > $clienteMover->getTreeLeft() &&
        $clienteDestino->getTreeRight() < $clienteMover->getTreeRight()) :
        $msgErro = 'Não é possível mover um cliente para baixo de um cliente da sua própria rede!';
    endif;

    $descricao = UsuarioPeer::getUsuarioLogado()->getNome() . 
        ' moveu o cliente ' . 
        $clienteMover->getNomeCompleto() . 
        ' para a rede do cliente ' .
        $clienteDestino->getNomeCompleto();

    if (!$msgErro) :

        // Move o cliente e toda sua rede para a rede do novo cliente pai
        $clienteMover->moveToLastChildOf($clienteDestino);

        $data = new DateTime();

        $alteracaoRede = new AlteracaoRede();
        $alteracaoRede->setClienteMovido($clienteMover->getId());
        $alteracaoRede->setClienteDestino($clienteDestino->getId());
        $alteracaoRede->setUsuarioId(UsuarioPeer::getUsuarioLogado()->getId());
        $alteracaoRede->setUpdater(UsuarioPeer::getUsuarioLogado()->getNome());
        $alteracaoRede->setData($data);
        $alteracaoRede->setDescricao($descricao);
        $alteracaoRede->save();

        // Atualiza o indicador com o id do novo cliente pai
        $clienteMover->setClienteIndicadorId($clienteDestino->getId());
        $clienteMover->setClienteIndicadorDiretoId($clienteDestino->getId());
        $clienteMover->save();

        $dataInicio = date('Y-m-d', strtotime('first day of this month'));
        $dataInicio = DateTime::createFromFormat('Y-m-d', $dataInicio)->setTime(0, 0, 0);
        $dataFim = date('Y-m-d', strtotime('last day of this month'));
        $dataFim = DateTime::createFromFormat('Y-m-d', $dataFim)->setTime(23, 59, 59);

        // Atualiza o cliente hotsite dos pedidos do cliente para o novo cliente pai
        // caso ele tenha realizado pedidos no hotsite desse antigo cliente pai
        $pedidos = PedidoQuery::create()
            ->filterByCliente($clienteMover)
            ->filterByPagamentoConfirmado(true)
            ->filterByStatus(PedidoPeer::STATUS_CANCELADO, Criteria::NOT_EQUAL)
            ->filterByCreatedAt($dataInicio, Criteria::GREATER_EQUAL)
            ->filterByCreatedAt($dataFim, Criteria::LESS_EQUAL)
            ->find();

        foreach ($pedidos as $pedido) :
            if ($pedido->getHotsiteClienteId()) :
                if (HotsitePeer::clientePossuiHotsite($clienteDestino)) :
                    $pedido->setHotsiteClienteId($clienteDestino->getId());
                    $pedido->save();
                else:
                    $pedido->setHotsiteClienteId(null);
                    $pedido->save();
                endif;
            endif;
        endforeach;

        // Exclui todos os extratos gerados por pedidos do cliente e sua rede no mês
        // para serem gerados novos de acordo com a nova rede
        $rede = $clienteMover->getClientesRede(true);

        $extratos = ExtratoQuery::create()
            ->joinPedido()
            ->usePedidoQuery()
            ->filterByClienteId($rede)
            ->endUse()
            ->filterByData($dataInicio, Criteria::GREATER_EQUAL)
            ->filterByData($dataFim, Criteria::LESS_EQUAL)
            ->find();

        /** @var $extrato Extrato */
        foreach ($extratos as $extrato) :
            $extrato->delete();
        endforeach;

        // Gera os novos extratos das bonificações dos pedidos do cliente e da sua rede no mês
        $pedidos = PedidoQuery::create()
            ->filterByClienteId($rede)
            ->filterByPagamentoConfirmado(true)
            ->filterByStatus(PedidoPeer::STATUS_CANCELADO, Criteria::NOT_EQUAL)
            ->filterByCreatedAt($dataInicio, Criteria::GREATER_EQUAL)
            ->filterByCreatedAt($dataFim, Criteria::LESS_EQUAL)
            ->find();

        foreach ($pedidos as $pedido) :
            if ($pedido->getPlano()) :
                $bonificacaoExpansao = new BonificacaoExpansao();
                $bonificacaoExpansao->distribuirBonus($pedido);
            endif;

            if ($pedido->temRecompra()) :
                $bonificacaoProdutividade = new BonificacaoProdutividade();
                $bonificacaoProdutividade->distribuirBonus($pedido);

                $clienteHotsite = $pedido->getCliente()->getClienteRelatedByClienteIndicadorId();

                $geraBonus = $clienteHotsite &&
                    !empty($this->getHotsiteClienteId()) &&
                    $clienteHotsite->getPlano()->getPercDescontoHotsite();

                if ($geraBonus) :
                    $bonificacaoEcommerce = new BonificacaoEcommerce();
                    $bonificacaoEcommerce->distribuiBonus($pedido);
                endif;

                $bonificacaoClientePreferencial = new BonificacaoClientePreferencial();
                $bonificacaoClientePreferencial->distribuirBonus($pedido);
            endif;
        endforeach;

        $session->getFlashBag()->add('success','Cliente movido com sucesso!');
    else :
        $session->getFlashBag()->add('error', $msgErro);
    endif;
endif;