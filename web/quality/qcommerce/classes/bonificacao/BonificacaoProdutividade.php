<?php


class BonificacaoProdutividade extends GerenciadorBonificacao implements BonificacaoPedidoInterface
{

    /**
     * BonificacaoProdutividade constructor.
     * @param PropelPDO|null $con
     */
    public function __construct(PropelPDO $con = null)
    {
        parent::__construct($con);
    }

    public function atualizarExtratoCliente(Cliente $cliente, $dataInicial, $dataFinal)
    {
        $extratos = ExtratoQuery::create()
            ->filterByCliente($cliente)
            ->filterByTipo(Extrato::TIPO_RESIDUAL)
            ->filterByData(['min' => $dataInicial, 'max' => $dataFinal])
            ->filterByOperacao('+')
            ->filterByBloqueado(true)
            ->find();

        /** @var $extrato Extrato */
        foreach ($extratos as $extrato) :
            $clienteExtrato = $extrato->getCliente();

            $nivel = $extrato->getPedido()->getCliente()->getTreeLevel() - $clienteExtrato->getTreeLevel();

            $qualificacao = PlanoCarreiraHistoricoPeer::getQualificacaoCliente(
                $clienteExtrato,
                date('n', $dataInicial->getTimestamp()),
                date('Y', $dataInicial->getTimestamp())
            );

            // Se o cliente ainda não se ativou no mês, nenhum extrato será desbloqueado
            if (!$qualificacao) :
                break;
            endif;

            // Se o cliente se ativou, porém não atingiu a qualificação necessária dependendo
            // do nível do cliente que gerou o seu extrato, esse extrato não será desbloqueado
            // Caso cumpra o requisito de qualificação, o extrato será desbloqueado
            switch ($nivel) :
                // case 0:
                case 1:
                case 2:
                    $extrato->setBloqueado(false);
                    $extrato->save();
                    break;
                case 3:
                    if ($qualificacao->getNivel() >= 2) :
                        $extrato->setBloqueado(false);
                        $extrato->save();
                    endif;
                    break;
                case 4:
                    if ($qualificacao->getNivel() >= 3) :
                        $extrato->setBloqueado(false);
                        $extrato->save();
                    endif;
                    break;
                case 5:
                    if ($qualificacao->getNivel() >= 4) :
                        $extrato->setBloqueado(false);
                        $extrato->save();
                    endif;
                    break;
                // case 6:
                //     if ($qualificacao->getNivel() >= 5) :
                //         $extrato->setBloqueado(false);
                //         $extrato->save();
                //     endif;
                //     break;
                // case 7:
                //     if ($qualificacao->getNivel() >= 6) :
                //         $extrato->setBloqueado(false);
                //         $extrato->save();
                //     endif;
                //     break;
            endswitch;
        endforeach;
    }

    public function distribuirBonus(Pedido $pedido)
    {
        // Não cria extrato se já existe para o mesmo pedido e tipo
        if ($this->existeExtrato(parent::TIPO_PRODUTIVIDADE, $pedido)) :
            return;
        endif;

        // Gera bonificação apenas de pedidos feitos por DIS ou CF
        if ($pedido->getCliente()->isClientePreferencial()) :
            return;
        endif;

        $totalPontos = $this->getTotalPontosPedido(parent::TIPO_PRODUTIVIDADE, $pedido);

        // Todos os níveis receberão o mesmo valor de bonificação
        $bonusDistribuir = $totalPontos * 0.07;

        // Se o bônus está zerado provavelmente não existe configuração de percentual/nivel para o plano do cliente,
        // Ou o pedido não possui itens que geram a bonificação de produtividade.
        // Portanto, não será criado extrato com valor zerado
        if ($bonusDistribuir <= 0) :
            return;
        endif;

        $data = new DateTime();

        // Criar extrato para o nível 0 (compra pessoal)
        // if ($pedido->getCliente()->getPlano() && $pedido->getCliente()->getPlano()->getParticipaProdutividade()) :
        //     $this->criaExtratoBonificacao(
        //         Extrato::TIPO_RESIDUAL,
        //         '+',
        //         $bonusDistribuir,
        //         $pedido->getCliente(),
        //         $data,
        //         sprintf('Bônus de recompra pessoal. Pedido %d', $pedido->getId()),
        //         true,
        //         ['PEDIDO_ID' => $pedido->getId()]
        //     );
        // endif;

        $patrocinador = $pedido->getCliente()->getPatrocinador();
        $nivel = 1;

        while ($patrocinador && $nivel <= 5) :
            // Não bonifica o cliente se o plano dele não permite bonificação de produtividade, ou se o cadastro for vago
            if (!$patrocinador->getPlano() || !$patrocinador->getPlano()->getParticipaProdutividade() || $patrocinador->getVago()) :
                $patrocinador = $patrocinador->getPatrocinador();
                continue;
            endif;

            $this->criaExtratoBonificacao(
                Extrato::TIPO_RESIDUAL,
                '+',
                $bonusDistribuir,
                $patrocinador,
                $data,
                sprintf('Bônus de Produtividade. Pedido %d - Cliente ' . $pedido->getCliente()->getNomeCompleto(), $pedido->getId()),
                true,
                ['PEDIDO_ID' => $pedido->getId()]
            );

            $nivel++;
            $patrocinador = $patrocinador->getPatrocinador();
        endwhile;
    }

    public function executarCompressaoDinamica(Cliente $cliente, $dataInicial, $dataFinal)
    {
        // Verifica todos os extratos que não foram desbloqueados
        $extratosPerdidos = ExtratoQuery::create()
            ->filterByCliente($cliente)
            ->filterByTipo(Extrato::TIPO_RESIDUAL)
            ->filterByData(['min' => $dataInicial, 'max' => $dataFinal])
            ->filterByOperacao('+')
            ->filterByBloqueado(true)
            ->find();

        $valorTotalPerdido = 0;

        /** @var $extrato Extrato */
        foreach ($extratosPerdidos as $extrato) :
            $this->remanejarExtrato(
                $extrato,
                ClientePeer::retrieveByPK($extrato->getCliente()->getClienteIndicadorId()),
                $dataInicial,
                $dataFinal
            );
            $valorTotalPerdido += $extrato->getPontos();
        endforeach;

        return $valorTotalPerdido;
    }

    public function getTotalBonusDistribuicao(Cliente $cliente, $dataInicial, $dataFinal)
    {
        /*

        //Verificação do valor total baseado na tabela de pedidos
        //(valor total dessa query deve ser igual ao total da query dos extratos)

        $valorTabelaPedidos = 0;

        $qualificacao = PlanoCarreiraHistoricoPeer::getQualificacaoCliente(
            $cliente,
            date('N', $dataInicial->getTimestamp()) + 1,
            date('Y', $dataFinal->getTimestamp())
        );

        $qualificacao = $qualificacao ? $qualificacao->getNivel() : 0;

        if ($qualificacao > 0) :
            $query = PedidoQuery::create()
                ->usePedidoItemQuery()
                ->useProdutoVariacaoQuery()
                ->useProdutoQuery()
                ->groupByPlanoId()
                ->endUse()
                ->endUse()
                ->endUse()
                ->filterByValorPontos(0, Criteria::GREATER_THAN)
                ->filterByStatus(PedidoPeer::STATUS_CANCELADO, Criteria::NOT_EQUAL)
                ->condition(
                    'cond1',
                    sprintf(
                        '%s IS NULL',
                        ProdutoPeer::PLANO_ID
                    )
                )
                ->condition(
                    'cond2',
                    sprintf(
                        'IFNULL(%s, %s) = %s',
                        PedidoPeer::HOTSITE_CLIENTE_ID,
                        PedidoPeer::CLIENTE_ID,
                        'clientePedido.ID'
                    )
                )
                ->condition(
                    'cond3',
                    sprintf(
                        '%s IS NOT NULL',
                        ProdutoPeer::PLANO_ID
                    )
                )
                ->condition(
                    'cond4',
                    sprintf(
                        '%s = %s',
                        PedidoPeer::CLIENTE_ID,
                        'clientePedido.ID'
                    )
                )
                ->combine(['cond1', 'cond2'], Criteria::LOGICAL_AND, 'comb1')
                ->combine(['cond3', 'cond4'], Criteria::LOGICAL_AND, 'comb2')
                ->combine(['comb1', 'comb2'], Criteria::LOGICAL_OR, 'comb3')
                ->joinCliente('clientePedido')
                ->setJoinCondition('clientePedido', 'comb3')
                ->where('clientePedido.tree_left >= ?', $cliente->getTreeLeft(), \PDO::PARAM_INT)
                ->where('clientePedido.tree_right <= ?', $cliente->getTreeRight(), \PDO::PARAM_INT)
                ->filterByDataPagamentoPeriodo($startDate, $endDate)
                ->useClienteQuery()
                ->_if($qualificacao <= 2)
                ->filterByTreeLevel($cliente->getTreeLevel() + 3, Criteria::LESS_EQUAL)
                ->_elseif($qualificacao == 3)
                ->filterByTreeLevel($cliente->getTreeLevel() + 4, Criteria::LESS_EQUAL)
                ->_elseif($qualificacao == 4)
                ->filterByTreeLevel($cliente->getTreeLevel() + 5, Criteria::LESS_EQUAL)
                ->_elseif($qualificacao == 5)
                ->filterByTreeLevel($cliente->getTreeLevel() + 6, Criteria::LESS_EQUAL)
                ->_else()
                ->filterByTreeLevel($cliente->getTreeLevel() + 7, Criteria::LESS_EQUAL)
                ->_endif()
                ->endUse()
                ->groupById()
                ->orderByCreatedAt(Criteria::DESC)
                ->find();

            foreach ($query as $pedido) :
                foreach ($pedido->getPedidoItems() as $pedidoItem) :
                    // Não considera pontos de pedidos de cliente preferencial
                    if ($pedido->getCliente()->getPlano() && $pedido->getCliente()->getPlano()->getPlanoClientePreferencial() == 1) :
                        break;
                    endif;

                    // Não considera pontos de itens de pedido que contém produtos ligados a um plano
                    if ($pedidoItem->getProdutoVariacao()->getProduto()->getPlano()) :
                        break;
                    endif;

                    $valorTabelaPedidos += $pedidoItem->getValorPontosUnitario() * $pedidoItem->getQuantidade();
                endforeach;
            endforeach;

            $valorTabelaPedidos = $valorTabelaPedidos * 0.07;
        endif;
        */

        // Verificação do valor total baseado no extrato do produtividade do cliente
        $extratos = ExtratoQuery::create()
            ->filterByCliente($cliente)
            ->filterByTipo(Extrato::TIPO_RESIDUAL)
            ->filterByData(['min' => $dataInicial, 'max' => $dataFinal])
            ->filterByOperacao('+')
            ->filterByBloqueado(false)
            ->find();

        $valor = 0;

        /** @var $extrato Extrato */
        foreach ($extratos as $extrato) :
            $valor += $extrato->getPontos();
        endforeach;

        return $valor;
    }

    public function remanejarExtrato(Extrato $extrato, Cliente $clienteRemanejar, $dataInicial, $dataFinal)
    {
        // Se o cliente for null, significa que já foram verificados todos os possíveis níveis para
        // remanejar o extrato, portanto, o extrato deve ser excluído
        if (!$clienteRemanejar) :
            $extrato->delete();
        endif;

        $nivel = $extrato->getPedido()->getCliente()->getTreeLevel() - $clienteRemanejar->getTreeLevel();

        $qualificacao = PlanoCarreiraHistoricoPeer::getQualificacaoCliente(
            $clienteRemanejar,
            date('N', $dataInicial->getTimestamp()) + 1,
            date('Y', $dataFinal->getTimestamp())
        );

        // O cliente não está qualificado para receber o extrato
        if (!$qualificacao) :
            $this->remanejarExtrato(
                $extrato,
                ClientePeer::retrieveByPK($clienteRemanejar->getClienteIndicadorId()),
                $dataInicial,
                $dataFinal
            );

            return;
        endif;

        // Se o cliente já possui extrato criado referente ao mesmo pedido, não deve atribuir outro extrato a ele
        if ($this->verificaClientePossuiExtrato($extrato, $clienteRemanejar, $dataInicial, $dataFinal)) :
            $this->remanejarExtrato(
                $extrato,
                ClientePeer::retrieveByPK($clienteRemanejar->getClienteIndicadorId()),
                $dataInicial,
                $dataFinal
            );

            return;
        endif;

        // Se o cliente se ativou, porém não atingiu a qualificação necessária dependendo
        // do nível do cliente que gerou o seu extrato, ele perde o extrato e será buscado
        // um novo cliente para receber
        switch ($nivel) :
            // case 0:
            case 1:
            case 2:
                $extrato->setCliente($clienteRemanejar);
                $extrato->setBloqueado(false);
                $extrato->save();
                break;
            case 3:
                if ($qualificacao->getNivel() >= 2) :
                    $extrato->setCliente($clienteRemanejar);
                    $extrato->setBloqueado(false);
                    $extrato->save();
                else :
                    remanejarExtrato($extrato);
                endif;
                break;
            case 4:
                if ($qualificacao->getNivel() >= 3) :
                    $extrato->setCliente($clienteRemanejar);
                    $extrato->setBloqueado(false);
                    $extrato->save();
                else :
                    remanejarExtrato($extrato);
                endif;
                break;
            case 5:
                if ($qualificacao->getNivel() >= 4) :
                    $extrato->setCliente($clienteRemanejar);
                    $extrato->setBloqueado(false);
                    $extrato->save();
                else :
                    remanejarExtrato($extrato);
                endif;
                break;
            // case 6:
            //     if ($qualificacao->getNivel() >= 5) :
            //         $extrato->setCliente($clienteRemanejar);
            //         $extrato->setBloqueado(false);
            //         $extrato->save();
            //     else :
            //         remanejarExtrato($extrato);
            //     endif;
            //     break;
            // case 7:
            //     if ($qualificacao->getNivel() >= 6) :
            //         $extrato->setCliente($clienteRemanejar);
            //         $extrato->setBloqueado(false);
            //         $extrato->save();
            //     else :
            //         remanejarExtrato($extrato);
            //     endif;
            //     break;
        endswitch;
    }

    /**
     * Retorna se um cliente já possui um extrato criado referente a um pedido
     *
     * @param Extrato $extrato
     * @param Cliente $cliente
     * @param $dataInicial
     * @param $dataFinal
     * @return bool
     * @throws PropelException
     */
    public function verificaClientePossuiExtrato(Extrato $extrato, Cliente $cliente, $dataInicial, $dataFinal)
    {
        $query = ExtratoQuery::create()
            ->filterByData(['min' => $dataInicial, 'max' => $dataFinal])
            ->filterByTipo(Extrato::TIPO_RESIDUAL)
            ->filterByPedido($extrato->getPedido())
            ->filterByCliente($cliente)
            ->findOne();

        return !$query;
    }

}
