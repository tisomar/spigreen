<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use QPress\Correios\CorreiosEndereco;

/**
 * Skeleton subclass for representing a row from the 'QP1_PEDIDO' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Pedido extends BasePedido
{
//    const ANDAMENTO = 'ANDAMENTO';
    const CANCELADO = 'CANCELADO';
    const FINALIZADO = 'FINALIZADO';
//    const AGUARDANDO = 'AGUARDANDO';
//    const DISTRIBUIDO_KIT = 'DISTRIBUIDO_KIT_PEDIDO';
//    const DISTRIBUIDO_PEDIDO = 'DISTRIBUIDO_PEDIDO';
//    const POSSUI_EXTRATO = 'POSSUI_EXTRATO';
//    const FIXO_PEDIDO_ID_PAGAMENTO = '00';
    const SITUACAO_CLEAR_SALE_APROVADO = 'APROVADO';
    const SITUACAO_CLEAR_SALE_REPROVADO = 'REPROVADO';

    private $oPedidoFormaPagamento;
    private $oListaPedidoFormaPagamento;
    private $oPedidoStatus;

    /**
     * Busca a última forma de pagamento do pedido.
     *
     * @return BasePedidoFormaPagamento
     */
    public function getPedidoFormaPagamento()
    {
        if (!$this->oPedidoFormaPagamento instanceof BasePedidoFormaPagamento) {
            $this->oPedidoFormaPagamento = PedidoFormaPagamentoQuery::create()
                ->filterByPedidoId($this->getId())
                ->_if($this->getClassKey() == 2)
                ->filterByStatus(
                    array(PedidoFormaPagamentoPeer::STATUS_APROVADO,
                        PedidoFormaPagamentoPeer::STATUS_PENDENTE),
                    Criteria::IN
                )
                ->_endif()
                ->orderByCreatedAt(Criteria::DESC)
                ->findOne();
        }

        return $this->oPedidoFormaPagamento;
    }

    /**
     * Busca a última forma de pagamento do pedido.
     *
     * @return BasePedidoFormaPagamento
     */
    public function getPedidoFormaPagamentoLista()
    {
        if (!$this->oListaPedidoFormaPagamento instanceof PropelObjectCollection || $this->oListaPedidoFormaPagamento->count() <= 0) {
            $this->oListaPedidoFormaPagamento = PedidoFormaPagamentoQuery::create()
                ->filterByPedidoId($this->getId())
                ->filterByStatus([
                    PedidoFormaPagamentoPeer::STATUS_APROVADO,
                    PedidoFormaPagamentoPeer::STATUS_PENDENTE
                ])
                ->find();
        }

        return $this->oListaPedidoFormaPagamento ?? [];
    }

    public function setPedidoFormaPagamento($v)
    {
        $this->oPedidoFormaPagamento = $v;
    }

    /**
     * Retorna a data de finalização do pedido quando houver uma forma de pagamento que esteja
     * em andamento ou aprovado.
     * Do contrário, retorna a data que o carrinho foi inicializado.
     *
     * @param string $format
     * @return mixed
     * @throws PropelException
     */
    public function getCreatedAt($format = 'Y-m-d H:i:s')
    {
        /*if (!is_null($this->getPedidoFormaPagamento())) {
            return $this->getPedidoFormaPagamento()->getCreatedAt($format);
        }*/
        return parent::getCreatedAt($format);
    }

    /**
     * Registra o cupom de desconto no carrinho.
     *
     * @param $cupom
     * @return bool
     * @throws PropelException
     */
    public function registerCupom($cupom)
    {

        $oCupom = CupomQuery::create()->findOneByCupom($cupom);

        if ($oCupom instanceof BaseCupom) {
            $this->setCupom($oCupom);
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * @throws PropelException
     */
    public function unregisterCupom()
    {
        if (!is_null($this->getCupom())) {
            FlashMsg::warning('Seu cupom de desconto foi removido. Você deverá inseri-lo novamente na página de pagamento.');
            $this->setCupom(null);
            $this->save();
        }
    }

    /**
     * @throws PropelException
     */
    public function unregisterDescontoPontos()
    {
        if ($descontoPontos = $this->getDescontoPontos()) {
            $con = Propel::getConnection();
            $con->beginTransaction();

            DescontoPagamentoPontosQuery::create()->filterById($descontoPontos->getId())->delete($con);

            $this->setDescontoPontos(null);
            $this->save($con);

            $con->commit();
        }
    }

    /**
     * @return Cupom
     */
    public function getCupom()
    {
        return parent::getCupom();
    }

    #############################################################
    #
    # STATUS DO PEDIDO E PAGAMENTO
    #
    #############################################################

    /**
     * Função responsável por atualizar as estatisticas dos produtos.
     * 
     * @param $id id do pedido
     */
    public function updateEstatistica($id)
    {
        $start = new Datetime('last year');
        $end = new Datetime('now');

        $PedidoItems = PedidoItemQuery::create()
            ->findByPedidoId($id);

        foreach ($PedidoItems as $item) {
            $produtoId = $item->getProdutoVariacao()->getProdutoId();

            $newEstatistica = ProdutoVendaEstatisticaPeer::getEstatisticaProduto(
                $start,
                $end,
                $produtoId
            );

            $estatistica = ProdutoVendaEstatisticaQuery::create()
                ->filterByProdutoId($produtoId)
                ->findOneOrCreate();
            
            $estatistica->setQuantidadeVendida($newEstatistica['TOTAL_VENDA']);
            $estatistica->setUpdateAt(new Datetime());
            $estatistica->save();
        }
    }


    public function avancaStatus($enviaNotificacao = true)
    {
        // Busca o último status
        $objLastPedidoStatus = $this->getLastPedidoStatus();

        // Cria, caso não exista
        if (is_null($objLastPedidoStatus)) :
            $objPedidoStatus = PedidoStatusQuery::create()
                ->orderById()
                ->findOne();

            $result = $this->createNewPedidoStatus($objPedidoStatus->getId());

            $this->updateEstatistica($this->getId());

            return $result;
        else :
            $objPedidoStatusHistorico = PedidoStatusHistoricoQuery::create()
                ->filterByPedidoId($this->getId())
                ->filterByPedidoStatusId($objLastPedidoStatus->getId())
                ->findOne();
            $objPedidoStatusHistorico->setIsConcluido(true);
            $objPedidoStatusHistorico->save();

            if ($objLastPedidoStatus->getId() == 1) :
                $this->confirmarPagamento();

                if ($this->isPagamentoMensalidade()) :
                    //pedidos que são pagamentos de mensalidade devem ser finalizados após a confirmação do pgamento.
                    $this->finalizar();

                    $this->updateEstatistica($this->getId());
                    return null;
                endif;
            endif;

            $objPedidoStatus = PedidoStatusQuery::create()
                ->filterByFrete($this->getFrete())
                ->filterByOrdem($objLastPedidoStatus->getOrdem(), Criteria::GREATER_THAN)
                ->orderByOrdem()
                ->findOne();

            if (is_null($objPedidoStatus) || $objLastPedidoStatus->getStatus() === 'FINALIZADO') :
                $this->finalizar();

                $this->updateEstatistica($this->getId());
                return null;
            endif;

            $result = $this->createNewPedidoStatus($objPedidoStatus->getId(), $enviaNotificacao);

            $this->updateEstatistica($this->getId());

            // Verifica se o cliente se ativou para desbloquear os seus extratos de cliente preferencial
            $clienteAtivo = ClientePeer::getClienteAtivoMensal($this->getCliente()->getId(),
                new DateTime(date('d-m-Y', strtotime('first day of this month'))),
                new DateTime(date('d-m-Y', strtotime('last day of this month'))));

            if ($clienteAtivo) :
                ExtratoPeer::desbloquearExtratoCliente($this->getCliente(), Extrato::TIPO_CLIENTE_PREFERENCIAL);
            endif;

            return $result;
        endif;

        $this->updateEstatistica($this->getId());
    }

    /**
     * Confirm payment.
     *
     * @throws PropelException
     */
    public function confirmarPagamento()
    {
        $formaPagamentoPendente = PedidoFormaPagamentoQuery::create()
            ->filterByPedidoId($this->getId())
            ->filterByStatus(PedidoFormaPagamentoPeer::STATUS_PENDENTE)
            ->find();

        foreach ($formaPagamentoPendente as $objFormaPagamento) :
            $objFormaPagamento->setStatus(PedidoFormaPagamentoPeer::STATUS_APROVADO)->save();
        endforeach;

        $objPlano = $this->getPlano();

        //Verifica se o pedido possui um kit de adesão. Se possuir, associa o plano desse kit ao cliente.
        if ($objPlano instanceof Plano) :
            $cliente = $this->getCliente();
            $cliente->setPlano($objPlano);
            $cliente->setLivreMensalidade(true);
            $cliente->setTipoConsumidor(1);
            $cliente->setDataAtivacao(new DateTime());
            $cliente->save();
        endif;

        if ($this->isPagamentoMensalidade()) :
            $this->getCliente()->renovaMensalidade();
        endif;

        if ($this->isPagamentoTaxaCadastro()) :
            $objCliente = $this->getCliente();
            $objCliente->setTaxaCadastro(0);
            $objCliente->save();
        endif;

        if ($this->getCliente()->getNaoCompra()) :
            $configPontuacaoMensal = ConfiguracaoPontuacaoMensalQuery::create()->findOneById(1);
            $comprasPagasMes = ConfiguracaoPontuacaoMensalPeer::getValorCompraMensalByCliente($this->getCliente());
            $comprasPagasMes += $this->getValorTotal(true);

            if ($configPontuacaoMensal->getValorCompra() <= $comprasPagasMes) :
                $objCliente = $this->getCliente();
                $objCliente->setNaoCompra(0);
                $objCliente->save();
                $dataAtual = DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
                $dataAtual->sub(new DateInterval('P1M'));
                $mesAnterior = $dataAtual->format('m');
                $mesPedido = $this->getCreatedAt('m');

                // TODO: WHAT IS THIS FOR!!!
                /** @todo Implementar o processo de pontos já liberados. */
                if ($mesAnterior == $mesPedido) :
                    $dataCompra = DateTime::createFromFormat('Y-m-d', $this->getCreatedAt('Y-m-d'));
                    $sqlExtrato = ExtratoQuery::create()
                        ->filterByClienteId($this->getClienteId())
                        ->filterByOperacao('-')
                        ->filterByPedidoId(null, Criteria::NOT_EQUAL)
                        ->filterByTipo('PAGAMENTO_PEDIDO', Criteria::NOT_EQUAL)
                        ->filterByTipo('PAGAMENTO_PARCIAL_PEDIDO', Criteria::NOT_EQUAL)
                        ->filterByDataInicial($dataCompra->format('Y-m') . '-01')
                        ->filterByDataFinal($dataCompra->format('Y-m-t'));
                    $arrExtrato = $sqlExtrato->find();

                    if (count($arrExtrato) > 0) :
                        /** @var Extrato $extrato */
                        foreach ($arrExtrato as $extrato) :
                            $extrato->delete();
                        endforeach;
                    endif;
                endif;

                $sqlExtratoMesAtual = ExtratoQuery::create()
                    ->filterByClienteId($this->getClienteId())
                    ->filterByOperacao('-')
                    ->filterByPedidoId(null, Criteria::NOT_EQUAL)
                    ->filterByTipo('PAGAMENTO_PEDIDO', Criteria::NOT_EQUAL)
                    ->filterByTipo('PAGAMENTO_PARCIAL_PEDIDO', Criteria::NOT_EQUAL)
                    ->filterByDataInicial(date('Y-m') . '-01')
                    ->filterByDataFinal(date('Y-m-t'));
                $arrExtratoMesAtual = $sqlExtratoMesAtual->find();
                // TODO: WHAT IS THIS FOR!!!

                if (count($arrExtratoMesAtual) > 0) :
                    /** @var Extrato $extratoAtual */
                    foreach ($arrExtratoMesAtual as $extratoAtual) :
                        $extratoAtual->delete();
                    endforeach;
                endif;
            endif;
        endif;

        // TODO: update this in future to use constructor
        $logger = new Logger('debug-channel');
        $logger->pushHandler(new StreamHandler('debug_app.log', Logger::DEBUG));

        if (!empty($objPlano)) :
            $bonificacaoExpansao = new BonificacaoExpansao();
            $bonificacaoExpansao->distribuirBonus($this);
        endif;

        if ($this->temRecompra()) :
            $bonificacaoProdutividade = new BonificacaoProdutividade();
            $bonificacaoProdutividade->distribuirBonus($this);

            $clienteHotsite = $this->getCliente()->getClienteRelatedByClienteIndicadorId();
            $geraBonus = $clienteHotsite &&
                !empty($this->getHotsiteClienteId()) &&
                $clienteHotsite->getPlano()->getPercDescontoHotsite();

            if ($geraBonus) :
                $bonificacaoEcommerce = new BonificacaoEcommerce();
                $bonificacaoEcommerce->distribuiBonus($this);
            endif;

            $bonificacaoClientePreferencial = new BonificacaoClientePreferencial();
            $bonificacaoClientePreferencial->distribuirBonus($this);

            $bonificacaoFrete = new BonificacaoFrete();
            $bonificacaoFrete->distribuirBonus($this);
        endif;

        // Distribui pontos no extrato preferencial para o cliente
        if ($this->getCliente()->isClientePreferencial()) :
            $extratoPreferencial = new ExtratoClientePreferencial();
            $extratoPreferencial->setCliente($this->getCliente());
            $extratoPreferencial->setPedido($this);
            $extratoPreferencial->setData(new DateTime());
            $extratoPreferencial->setOperacao('+');
            $extratoPreferencial->setPontos($this->getValorPontos());
            $extratoPreferencial->setObservacao(sprintf('Pontos recebidos do pedido: %s.', $this->getId()));
            $extratoPreferencial->save();
        endif;

        $this->gerarFluxoCaixa();

        $objIntegracaoPedido = new IntegracaoPedido();
        $objIntegracaoPedido->setPedido($this);
        $objIntegracaoPedido->save();
    }

    /**
     * @param $pedidoStatusId
     * @param bool $enviaNotificacao
     * @return PedidoStatusHistorico
     * @throws PropelException
     */
    public function createNewPedidoStatus($pedidoStatusId, $enviaNotificacao = true)
    {

        $pedidoStatusId = $this->isNotExpedicaoPedido($pedidoStatusId) ? 4 : $pedidoStatusId; // pula o step de expedição quando o pedido for retirado em loja
       
        $objPedidoStatusHistorico = new PedidoStatusHistorico();
        $objPedidoStatusHistorico->setPedidoId($this->getId());
        $objPedidoStatusHistorico->setPedidoStatusId($pedidoStatusId);
        $objPedidoStatusHistorico->setCreatedAt(new DateTime());
        $objPedidoStatusHistorico->save();

        $this->oPedidoStatus = $objPedidoStatusHistorico->getPedidoStatus();

        if ($pedidoStatusId > 1 && $enviaNotificacao) :
            try {
                \QPress\Mailing\Mailing::pedidoNovoStatus($this);
            } catch (Exception $e) {}
        endif;

        return $objPedidoStatusHistorico;
    }

    public function finalizar()
    {
        $usuario = UsuarioPeer::getUsuarioLogado();
        $usuario_id = $usuario->getId();

        $this->setStatus(PedidoPeer::STATUS_FINALIZADO);
        $this->setUpdaterUserId($usuario_id);
        $this->save();

        \QPress\Mailing\Mailing::pedidoFinalizado($this);

        //Envia email solicitando a avaliacao do cliente (apenas se está habilitado)
        if (Config::get('avaliacao.is_habilitado')) {
            \QPress\Mailing\Mailing::enviarAvaliacaoPedido($this);
        }
    }

    public function cancelar()
    {
        
        $con = Propel::getConnection();

        try {
            $con->beginTransaction();

            if($this->getLastPedidoStatus() !== null) :

                $pgtoConfirmed = $this->getLastPedidoStatus()->getId() > 1 && $this->getLastPedidoStatus()->getId() < 6;

                foreach ($this->getPedidoItems() as $pedidoItem) {
                    /* @var $pedidoItem PedidoItem */

                    $saidaEstque = EstoqueProdutoQuery::create()
                        ->filterByPedidoId($this->getId())
                        ->filterByProdutoVariacaoId($pedidoItem->getProdutoVariacaoId())
                        ->findOne($con);

                    if (!is_null($saidaEstque)) {
                        $saidaEstque->delete($con);
                    }

                    $pedidoItem->getProdutoVariacao()->aumentarEstoque($pedidoItem->getQuantidade());

                    if ($pgtoConfirmed && !is_null($pedidoItem->getPlanoId())) {
                        $cliente = $this->getCliente();
                        /** @var $cliente Cliente */
                        $cliente->setPlanoId(null);
                        $cliente->save();
                    }
                }

                $usuario = UsuarioPeer::getUsuarioLogado();
                $usuario_id = $usuario->getId() ?? null;

                $this->setStatus(PedidoPeer::STATUS_CANCELADO);
                $this->setUpdaterUserId($usuario_id);
                $this->save();

                foreach ($this->getPedidoFormaPagamentoLista() as $objFormaPagamento) :
                    if($objFormaPagamento->getStatus() === PedidoFormaPagamentoPeer::STATUS_PENDENTE) :
                        $objFormaPagamento->setStatus(PedidoFormaPagamentoPeer::STATUS_CANCELADO)->save();
                    endif;
                endforeach;

                // Aplica estorno no extrato caso o pedido tenha sido pago com bônus
                $arrExtratos = ExtratoQuery::create()->filterByPedidoId($this->getId())->find();

                $pagamentosBonus = [
                    Extrato::TIPO_PAGAMENTO_PEDIDO,
                    Extrato::TIPO_PAGAMENTO_PARCIAL_PEDIDO,
                    Extrato::TIPO_BONUS_FRETE
                ];

                foreach ($arrExtratos as $extrato) {
                    /** @var $extrato Extrato */

                    if ($extrato->getOperacao() == '+'
                        && !in_array($extrato->getTipo(), $pagamentosBonus)) {
                        $extrato->delete();
                    }

                    if ($extrato->getOperacao() == '-' && (in_array($extrato->getTipo(), $pagamentosBonus))) {
                        $tipoBonus = in_array($extrato->getTipo(), [
                                Extrato::TIPO_PAGAMENTO_PEDIDO,
                                Extrato::TIPO_PAGAMENTO_PARCIAL_PEDIDO
                            ])
                            ? 'bônus'
                            : 'bônus frete';

                        $observacaoEstorno = "Estorno de {$tipoBonus} pelo cancelamento do pedido {$this->getId()}.";

                        $extratoEstorno = $extrato->copy();
                        $extratoEstorno->setOperacao('+');
                        $extratoEstorno->setObservacao($observacaoEstorno);
                        $extratoEstorno->setData(new Datetime());
                        $extratoEstorno->save();
                    }
                }

                // Aplica o estorno no extrato de cliente preferencial caso o pedido
                // tenha sido pago com pontos
                $arrExtratos = ExtratoClientePreferencialQuery::create()
                    ->filterByPedido($this)
                    ->filterByOperacao('+')
                    ->find();

                foreach ($arrExtratos as $extratoPreferencial) :
                    /** @var $extratoPreferencial ExtratoClientePreferencial */

                    $gerenciador = new GerenciadorPontosClientePreferencial();

                    $gerenciador->criarExtrato(
                        $this,
                        new DateTime(),
                        '-',
                        $extratoPreferencial->getPontos(),
                        sprintf('Estorno de pontos pelo cancelamento do pedido %s', $this->getId())
                    );
                endforeach;

                // Aplica o estorno no extrato de cliente preferencial caso o pedido
                // tenha sido pagp com pontos
                $extratoPreferencial = ExtratoClientePreferencialQuery::create()
                    ->filterByPedido($this)
                    ->findOne();

                if ($extratoPreferencial) :
                    $gerenciador = new GerenciadorPontosClientePreferencial();

                    $gerenciador->criarExtrato(
                        $this,
                        new DateTime(),
                        '+',
                        $extratoPreferencial->getPontos(),
                        sprintf('Estorno do pagamento com pontos pelo cancelamento do pedido %s', $this->getId())
                    );
                endif;

                $this->updateEstatistica($this->getId());

                $con->commit();

                \QPress\Mailing\Mailing::pedidoCancelado($this);

            endif;
        } catch (Exception $e) {
            $con->rollBack();
        }
    }

    /**
     *
     * @return PedidoStatus
     */
    public function getLastPedidoStatus()
    {
        if (!$this->oPedidoStatus instanceof BasePedidoStatus) {
            $this->oPedidoStatus = PedidoStatusQuery::create()
                //->filterByFrete($this->getFrete())
                ->usePedidoStatusHistoricoQuery()
                ->filterByPedidoId($this->getId())
                ->orderByCreatedAt(Criteria::DESC)
                ->endUse()
                ->findOne();
        }

        return $this->oPedidoStatus;
    }

    public function isFinalizado()
    {
        return $this->getStatus() == PedidoPeer::STATUS_FINALIZADO;
    }

    public function isCancelado()
    {
        return $this->getStatus() == PedidoPeer::STATUS_CANCELADO;
    }

    public function isAndamento()
    {
        return $this->getStatus() == PedidoPeer::STATUS_ANDAMENTO;
    }

    public function getLinkSegundaVia()
    {
        return $this->getUrlAcesso();
    }

    public function getStatusLabel()
    {

        $options = array(
            PedidoPeer::STATUS_ANDAMENTO => array(
                'label' => 'warning',
                'icon' => 'icon-time',
                'title' => 'Andamento'
            ),
            PedidoPeer::STATUS_FINALIZADO => array(
                'label' => 'success',
                'icon' => 'icon-ok',
                'title' => 'Finalizado'
            ),
            PedidoPeer::STATUS_CANCELADO => array(
                'label' => 'danger',
                'icon' => 'icon-ban-circle',
                'title' => 'Cancelado'
            ),
        );

        $title = $label = $icon = null;

        extract($options[$this->getStatus()]);

        return label($title, $label, $icon);
    }

    public function isAbandonado()
    {

        $updated = new DateTime($this->getUpdatedAt());
        $now = new DateTime("now");

        $diff = $updated->diff($now);

        return $this->getClassKey() == PedidoPeer::CLASS_KEY_CARRINHO
            && !is_null($this->getClienteId())
            && ($diff->h + $diff->d * 24) > PedidoPeer::CONFIGURACAO_HORAS_CARRINHO_ABANDONADO;
    }

    public function getUrlReativation()
    {
        return get_url_site() . '/carrinho/reactivate/' . md5($this->getId() . $this->getClienteId());
    }

    #############################################################
    #
    # AÇÕES DO CARRINHO
    #
    #############################################################

    /**
     * Adiciona um item ao carrinho
     *
     * @param BasePedidoItem $item
     * @return BasePedido
     */
    public function addItem(BasePedidoItem $item)
    {
        if ($this->getClassKey() != 1) {
            $item->setPedido($this);
            $item->save();
            $this->setValorEntrega(0)->save();

            $this->recalcularPedido();
        }

        return $this;
    }

    /**
     * Remove um item do carrinho
     *
     * @param BasePedidoItem or integer $item
     */
    public function removeItem($item)
    {
        if ($this->getClassKey() != 1) {
            if (is_numeric($item)) {
                $item = PedidoItemQuery::create()->filterByPedido($this)->findPk($item);
            }

            if ($item instanceof PedidoItem) {
                $item->delete();

                $this->recalcularPedido();
            }
        }

        return $this;
    }

    public function recalcularPedido() {
        $cliente = ClientePeer::getClienteLogado(true);

        $plano = $this->getPlano();
        $planoCliente = !empty($cliente) ? $cliente->getPlano() : null;

        $valorTotal = 0;

        foreach ($this->getPedidoItems() as $item):
            $variacao = $item->getProdutoVariacao();
            $produto = $variacao->getProduto();

            if (!empty($produto->getPlanoId()) && $produto->isProdutoSimples()):
                $valorTotal += $item->getValorUnitario() * $item->getQuantidade();

                continue;
            endif;

            $valorProduto = $variacao->getValorBase();

            if (!empty($cliente)):
                list($valorFidelidade) = $variacao->getValorFidelidade();
                $valorProduto = min($valorProduto, $valorFidelidade);
            endif;

            if (!empty($plano) && (empty($planoCliente) || $plano->getNivel() > $planoCliente->getNivel())):
                $valorPlano = $variacao->getValorProdutoPlano($plano);
                $valorProduto = min($valorProduto, $valorPlano);
            endif;

            $item->setValorUnitario($valorProduto);
            $item->save();

            $valorTotal += $valorProduto * $item->getQuantidade();
        endforeach;

        $this->setValorItens($valorTotal);
        $this->save();
    }

    /**
     * Conta a quantidade de itens
     *
     * @return integer
     */
    public function countItems()
    {
        return $this->countPedidoItems();
    }

    /**
     * Conta a quantidade total dos itens adicionados
     *
     * @return integer
     */
    public function countQuantidadeTotal()
    {
        $quantidade = 0;

        foreach ($this->getPedidoItems() as $item) {
            $quantidade += $item->getQuantidade();
        }

        return $quantidade;
    }

    public function getValorDesconto($withDiscountByPayment = true)
    {
        $desconto = 0;
        if ($withDiscountByPayment) {
            $desconto += $this->getValorDescontoBy(PedidoFormaPagamentoPeer::OM_CLASS);
        }

        $desconto += $this->getValorDescontoBy(CupomPeer::OM_CLASS);
        $desconto += $this->getValorDescontoBy(DescontoPagamentoPontosPeer::OM_CLASS);

        return $desconto;
    }

    public function getValorDescontoBy($reference)
    {

        $desconto = 0;

        switch ($reference) {
            case CupomPeer::OM_CLASS:
                if (!is_null($this->getCupom())) {
                    if ($this->getCupom()->getTipoDesconto() == CupomPeer::TIPO_DESCONTO_PORCENTAGEM) {
                        $desconto += $this->getValorItens() * $this->getCupom()->getValorDesconto() / 100;
                    } else {
                        $desconto += $this->getCupom()->getValorDesconto();
                    }
                }

                break;

            case DescontoPagamentoPontosPeer::OM_CLASS:
                if ($this->getDescontoPontos()) {
                    $desconto += $this->getDescontoPontos()->getValorDesconto();
                }
                break;

            case PedidoFormaPagamentoPeer::OM_CLASS:
                if (!is_null($this->getPedidoFormaPagamento())) {
                    $desconto = $this->getPedidoFormaPagamento()->getValorDesconto();
                }

                break;
        }

        return $desconto;
    }

    /**
     * Calcula o valor total dos itens
     *
     * @return \Carrinho
     * @throws PropelException
     */
    public function calculateItemsValorTotal()
    {
        $this->clearPedidoItems();
        $itemsTotal = 0;

        foreach ($this->getPedidoItems() as $item) :
            $itemsTotal += $item->getValorTotal();
        endforeach;

        $this->setValorItens($itemsTotal);

        return $this;
    }

    /**
     * Calcula o valor total do carrinho
     *
     * @param bool $withDiscountByPayment
     * @return float
     */
    public function getValorTotal($withDiscountByPayment = true)
    {
        $total = $this->getValorItens() + $this->getValorEntrega() - $this->getValorDesconto($withDiscountByPayment);

        if ($total < 0) :
            return 0;
        endif;

        return $total;
    }

    public function getPesoTotal()
    {
        $peso = 0;

        /* @var $item PedidoItem */
        foreach ($this->getPedidoItems() as $item) {
            $peso += $item->getPeso() * $item->getQuantidade();
        }

        return $peso;
    }

    /**
     * Verifica se o carrinho está vazio
     *
     * @return boolean
     */
    public function isEmpty()
    {
        $this->clearPedidoItems();
        return $this->getPedidoItems()->isEmpty();
    }

    #############################################################
    #
    # FRETE
    #
    #############################################################

    public function consultaFrete($modalidade, $cep = null)
    {
        $package = $this->generatePackage($cep);
        return $this->frete_manager->consultar($modalidade, $package);
    }

    /**
     * Generates a package for freight calculation.
     * Uses QPress PackageItem and Package Class.
     *
     * @param null $cep
     * @return \QPress\Frete\Package\Package
     * @throws PropelException
     */
    public function generatePackage($cep = null)
    {
        $cepFrom = $this->getCepOrigem();

        if (!is_null($cep)) :
            $cepTo = $cep;
        elseif ($this->getEndereco() instanceof BaseEndereco) :
            $cepTo = $this->getEndereco()->getCep(); // Uses users profile for CEP
        endif;

        if (is_null($cepTo) || !isset($cepTo[7])) :
            throw new Exception('Não foi possivel resolver o cep destino.');
            //return false;
        endif;

        // Cria um pacote para enviar aos meios de entrega.
        $package = new \QPress\Frete\Package\Package();

        // Adiciona os parametros de origem e destino
        $package->setClient(new \QPress\Frete\Package\PackageClient(
            $cepFrom,   // cep de origem
            $cepTo      // cep de destino
        ));

        // Adiciona os itens a este pacote, pois cada meio de entrega possui suas particularidades.
        foreach ($this->getPedidoItemsJoinProdutoVariacao() as $item) :
            if ($item->getProdutoVariacao()->getProduto()->getTaxaCadastro()) :
                continue;
            endif;

            $package->addItem(new \QPress\Frete\Package\PackageItem(
                $item->getId(),
                $item->getProdutoVariacao()->getProduto()->getPeso(),
                $item->getProdutoVariacao()->getProduto()->getAltura(),
                $item->getProdutoVariacao()->getProduto()->getComprimento(),
                $item->getProdutoVariacao()->getProduto()->getLargura(),
                $item->getQuantidade(),
                $item->getValorTotal()
            ));
        endforeach;

        return $package;
    }

    /**
     * Retorna o número máximo de parcelas para um valor
     *
     * @param float $floatValor
     * @return integer Numero de parcelas
     */
    public static function getMaxNumeroParcelasByValor($floatValor)
    {
        // Pegando quantidade de parcelas (pode ser número quebrado)
        // Importante: Utiliza-se a função floor, pois é necessário sempre arrendondar o número quebrado para baixo
        // Assim tem-se certeza que será possível fazer o parcelamento daquele valor e não irá ultrapassar o valor mínimo da parcela.
        // Quando um $numParcelas quebrado passa do .5, ex.: 3.6 tem-se um número $numParcelas que fará com o que o valor das parcelas
        // seja maior que o ValorMinParcelas()
        $numParcelas = floor($floatValor / Config::get('valor_minimo_parcela'));

        // Se o $floatValor for maior que o getValorMinParcelas() então o resultado será menor que 1,
        // sendo necessário corrigir e informar que não haverá parcelamento
        if ($numParcelas < 1) {
            $numParcelas = 1;
        }

        if ($numParcelas > Config::get('numero_maximo_parcelas')) {
            $numParcelas = Config::get('numero_maximo_parcelas');
        }

        return $numParcelas;
    }

    /**
     * Deleta os itens pedidos quando for deletar um pedido.
     * @param PropelPDO $con
     * @return bool
     * @throws Exception
     * @throws PropelException
     */
    public function preDelete(PropelPDO $con = null)
    {
        if ($this->getClassKey() == 2) {
            $this->getPedidoItems()->delete($con);
        }
        return parent::preDelete();
    }

    /**
     * Retorna o tipo de pagamento para a integração com a ClearSale.
     *
     * @param PropelPDO $con
     * @return string
     */
    public function getTipoPagamentoClearSale(PropelPDO $con = null)
    {
        /*
         1 Cartão de Crédito
         2 Boleto Bancário
         3 Débito bancário
         4 Débito Bancário ? Dinheiro
         5 Débito Bancário ? Cheque
         6 Transferência Bancária
         7 Sedex a Cobrar
         8 Cheque
         9 Dinheiro
         10 Financiamento
         11 Fatura
         12 Cupom
         13 Multicheque
         14 Outros
         */

        //Por enquanto só vamos integrar cartao
        $tipoPagamento = '1';

        return $tipoPagamento;
    }

    /**
     * Retorna o nome do cliente enviado a ClearSale.
     *
     * @param PropelPDO $con
     * @return string
     */
    public function getNomeClienteClearSale(PropelPDO $con = null)
    {
        $nome = '';

        if ($cliente = $this->getCliente($con)) {
            if ($cliente->isPessoaJuridica()) {
                $nome = (string)$cliente->getRazaoSocial();
            } else {
                $nome = (string)$cliente->getNomeCompleto();
            }
        }

        return $nome;
    }

    /**
     * Retorna o documento de cobrança enviado a ClearSale.
     *
     * @param PropelPDO $con
     * @return string
     */
    public function getCobrancaDocumentoClearSale(PropelPDO $con = null)
    {
        $documento = '';

        if ($cliente = $this->getCliente($con)) {
            if ($cliente->isPessoaJuridica()) {
                $documento = (string)$cliente->getCnpj();
            } else {
                $documento = (string)$cliente->getCpf();
            }
        }

        return $documento;
    }

    public function aprovaClearSale(PropelPDO $con = null, $save = true)
    {
        $this->setSituacaoClearSale(self::SITUACAO_CLEAR_SALE_APROVADO);

        $this->avancaStatus();

        if ($save) {
            $this->save($con);
        }
    }

    public function reprovaClearSale(PropelPDO $con = null, $save = true)
    {
        $this->setSituacaoClearSale(self::SITUACAO_CLEAR_SALE_REPROVADO);

        if ($save) {
            $this->save($con);
        }
    }

    public function getDataConfirmacaoPagamento($format = 'Y-m-d H:i:s')
    {
        $psh = PedidoStatusHistoricoQuery::create()
            ->filterByPedido($this)
            ->filterByPedidoStatusId(1)
            ->filterByIsConcluido(1)
            ->findOne();

        if (is_null($psh)) {
            return null;
        }

        return $psh->getUpdatedAt($format);
    }

    /**
     * Caso o carrinho possua um kit de adesão de plano, retorna o plano desse kit.
     *
     * @return Plano|null
     * @throws PropelException
     */
    public function getPlano()
    {
        /* @var $objPedidoItem PedidoItem */
        foreach ($this->getPedidoItems() as $objPedidoItem) {
            if (is_null($objPedidoItem->getProdutoVariacao())) {
                $this->removeItem($objPedidoItem);
            }

            $objProduto = $objPedidoItem->getProdutoVariacao()->getProduto();
            if ($objProduto && $objProduto->isKitAdesao() && ($objPlano = $objProduto->getPlano())) {
                return $objPlano;
            }
        }

        return null;
    }

    /**
     * Verifica se o carrinho tem algum item que não seja Kit/Plano
     *
     * @return boolean
     * @throws PropelException
     */
    public function temRecompra()
    {
        /* @var $objPedidoItem PedidoItem */
        foreach ($this->getPedidoItems() as $objPedidoItem) {
            if (is_null($objPedidoItem->getProdutoVariacao())) {
                $this->removeItem($objPedidoItem);
            }

            $objProduto = $objPedidoItem->getProdutoVariacao()->getProduto();
            if ($objProduto && !$objProduto->isKitAdesao()) {
                return true;
            }
        }

        return false;
    }


    /**
     * Verifica se o pedido é um pagamento de mensalidade (possui apenas um item que é uma mensalidade).
     *
     * @return boolean
     */
    public function isPagamentoMensalidade()
    {
        $possuiMensalidade = false;

        /* @var $objPedidoItem PedidoItem */
        foreach ($this->getPedidoItems() as $objPedidoItem) {
            $objProduto = $objPedidoItem->getProdutoVariacao()->getProduto();
            if ($objProduto && $objProduto->isMensalidade()) {
                $possuiMensalidade = true;
            } else {
                return false; /* possui um item que nao é mensalidade */
            }
        }

        return $possuiMensalidade;
    }

    /**
     * Verifica se o pedido é um pagamento da taxa de cadastro
     *
     * @return boolean
     * @throws PropelException
     */
    public function isPagamentoTaxaCadastro()
    {
        $possuiMensalidade = false;

        /* @var $objPedidoItem PedidoItem */
        foreach ($this->getPedidoItems() as $objPedidoItem) {
            $objProduto = $objPedidoItem->getProdutoVariacao()->getProduto();
            if ($objProduto && $objProduto->getTaxaCadastro()) {
                $possuiMensalidade = true;
                break;
            }
        }

        return $possuiMensalidade;
    }

    /**
     * Test if kit.
     *
     * @param $con
     * @return bool
     * @throws PropelException
     */
    public function isKitAdesaoPedido($con)
    {
        $temKitAdesao = false;

        foreach ($this->getPedidoItems(null, $con) as $item) :
            $produto = $item->getProdutoVariacao($con)->getProduto($con);

            if ($produto && $produto->isKitAdesao()) :
                $temKitAdesao = true;
                break;
            endif;
        endforeach;

        return $temKitAdesao;
    }

    /**
     * @return array|bool
     */
    public function gerarFluxoCaixa()
    {

        return FluxoCaixaPeer::gerarFluxoCaixa($this);
    }

    /**
     * Calculca o valor total de pontos de carreira dos produtos do pedido
     * @param $tipoExtrato - tipo do extrato ["INDICACAO_DIRETA", "INDICACAO_INDIRETA", "RESIDUAL"]
     * @return int
     */
    public function getTotalPontosProdutos($tipoExtrato = null) {
        $valorTotal = 0;

        foreach ($this->getPedidoItems() as $item) :
            // Se for indicacao direta ou indireta, só considera os pontos dos kits
            // kit com id 2 está desativado, mas consta em alguns pedidos feitos antes da desativação
            // Se for recompra, só considera os produtos que não são kit
            if (in_array($tipoExtrato, [Extrato::TIPO_INDICACAO_DIRETA, Extrato::TIPO_INDICACAO_INDIRETA, Extrato::TIPO_INDICACAO])) :
                if ($item->getProdutoVariacao()->getProduto()->isKitAdesao()) :
                    $valorTotal += $item->getValorPontosUnitario() * $item->getQuantidade();
                endif;
            else :
                if (!$item->getProdutoVariacao()->getProduto()->isKitAdesao()) :
                    $valorTotal += $item->getValorPontosUnitario() * $item->getQuantidade();
                endif;
            endif;
        endforeach;

        return $valorTotal;
    }

    /**
     * Retorna o cep de origem para a entrega (frete) do pedido
     * conforme configuração no cadastro de estado e centro de distribuição
     *
     * @return string
     * @throws PropelException
     */
    public function getCepOrigem()
    {
        $estado = '';

        if ($this->getEndereco()) :
            $estado = $this->getEndereco()
                ->getCidade()
                ->getEstado()
                ->getSigla();
        else :
            $estado = CorreiosEndereco::consultaCepViaCep('89035510');
            $estado = $estado['uf'];
        endif;

        /** @var $estadoCentro EstadoCentroDistribuicao */
        $estadoCentro = EstadoCentroDistribuicaoQuery::create()
            ->useEstadoQuery()
                ->filterBySigla($estado)
            ->endUse()
            ->where('EstadoCentroDistribuicao.CentroDistribuicaoId IS NOT NULL')
            ->findOne();

        $cep = Config::get('cep_origem');

        if ($estadoCentro) :
            $cep = $estadoCentro->getCentroDistribuicao()->getCep();
            $cep = str_replace('-', '', $cep);

            // Seta o centro de distribuição para indicar a origem da entrega do pedido
            $this->setCentroDistribuicao($estadoCentro->getCentroDistribuicao());
            $this->save();
        endif;

        return $cep;
    }

    public function getPedidoItems($criteria = null, $con = null) {
        $pedidoItemQuery = PedidoItemQuery::create(null, $criteria)
            ->filterByPlanoId(null, Criteria::ISNULL);

        return parent::getPedidoItems($pedidoItemQuery);
    }

    public function getPedidoItemsAll($planoId) {
        $pedidoItemQuery = PedidoItemQuery::create()
            ->filterByPlanoId($planoId);

        return parent::getPedidoItems($pedidoItemQuery);
    }

    public function getValorAcessorios()
    {
        $valorAcessorios = 0;

        foreach ($this->getPedidoItems() as $item) :
            foreach ($item->getProdutoVariacao()->getProduto()->getProdutoCategorias() as $categoria) :
                // 55 -> ACESSÓRIOS
                if ($categoria->getCategoriaId() == 55) :
                    $valorAcessorios += $item->getQuantidade() * $item->getValorUnitario();
                endif;
            endforeach;
        endforeach;

        return $valorAcessorios;
    }

    public function temPagamentoPendente()
    {
        $temPagamentoPendente = PedidoFormaPagamentoQuery::create()
            ->filterByPedidoId($this->getId())
            ->filterByStatus(PedidoFormaPagamentoPeer::STATUS_PENDENTE)
            ->count() > 0;

        return $temPagamentoPendente;
    }

    public function isNotExpedicaoPedido($nextStatusId) {
        if($this->getFrete() === 'retirada_loja' && $nextStatusId == 2) : // SE O PEDISO FOR RETIRADO EM LOJA E O PROXIMO STATUS FOR DE EXPEDIÇÂO
            return true;
        endif;

        return false;
    }

    public function avisaClienteComCodigosFrete($codigosFrete) {

        $avisar = false;
        switch ($codigosFrete) {
            case 'setCodigoRastreio':
            case 'setNumeroNotaFiscal':
            case 'setLinkRastreio':
            case 'setTransportadoraNome';
                $avisar = true;
                break;
        }

        if($avisar) :
            try {
                \QPress\Mailing\Mailing::pedidoNovoStatus($this);
            } catch (Exception $e) {}
        endif;

        return true;
    }

    public function getCentroDistribuicaoEstoqueByEstadoCliente($centro_distribuicao_id = null) {
        $estadoCliente = $this->getEndereco() ? $this->getEndereco()->getCidade()->getEstado()->getSigla() : 'MT';

        // ES não entrega para MT, RO e SP
        $arEstadosEntregaES = [ 'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'ES', 'GO', 'MA', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RR', 'SC', 'SE', 'TO'];

        // ID 1 = centrodistrobuicao ES
        // ID 3 = centrodistrobuicao CUIABÀ
        // ID 4 = centrodistrobuicao SP
        $centroDistribuicaoId = 1;

        if(in_array($estadoCliente, $arEstadosEntregaES)) :
            $centroDistribuicaoId = 1; 
        endif;

        switch ($estadoCliente) {
            case 'MT':
            case 'RO':
                $centroDistribuicaoId = 3;
                break;
            case 'SP':
                $centroDistribuicaoId = 4;
                break;
        }

        $centroDistribuicaoId = $centro_distribuicao_id ?? $centroDistribuicaoId;

        $avisoEstoqueNegativo = [];
        foreach($this->getPedidoItems() as $pedidoItem) :

            $variacao = $pedidoItem->getProdutoVariacao();
            $produto = $variacao->getProduto();
            if (!empty($produto->getPlanoId())):
                $produtoKit = $variacao->getProdutoNomeCompleto();
                
                foreach ($this->getPedidoItemsAll($produto->getPlanoId()) as $objPedidoItem):
                    $variacao = $objPedidoItem->getProdutoVariacao();
                    $produto = $variacao->getProduto();

                    $estoqueDisponivel = $variacao->getEstoqueAtualCD($centroDistribuicaoId);
                    $qtdItens = $objPedidoItem->getQuantidade();

                    if($estoqueDisponivel < $qtdItens) :
                        $avisoEstoqueNegativo[] = ["Seu pedido contém $qtdItens item/itens do produto {$produto->getNome()} <br> em nosso centro de distribuição existe {$estoqueDisponivel} deste produto."];
                    endif;
                endforeach;
            else:
                $estoqueDisponivel = $pedidoItem->getProdutoVariacao()->getEstoqueAtualCD($centroDistribuicaoId);
                $qtdItens = $pedidoItem->getQuantidade();
           
                if($estoqueDisponivel < $qtdItens) :
                    $avisoEstoqueNegativo[] = ["Seu pedido contém $qtdItens item/itens do produto {$pedidoItem->getProdutoVariacao()->getProduto()->getNome()} <br> em nosso centro de distribuição existe {$estoqueDisponivel} deste produto."];
                endif;
            endif;

        endforeach;

        return [$avisoEstoqueNegativo, $centroDistribuicaoId];
    }

    public function getBlockItemTotransporteGollog() {
        $blockTransporte = false;
        foreach($this->getPedidoItems() as $pedidoItem) :

            $variacao = $pedidoItem->getProdutoVariacao();
            $produto = $variacao->getProduto();

            // CHECANDO DOS KITS OURO, PRATA, BRONZE
            if(!$produto->isProdutoSimples()) :
                
                $arrProdutoCompostos = ProdutoCompostoQuery::create()->findByProdutoId($produto->getId());

                foreach ($arrProdutoCompostos as $objProdutoComposto) :
                    $produtoName = $objProdutoComposto->getProdutoVariacao()->getProduto()->getNome();

                    if(strpos($produtoName, 'alcool') || strpos($produtoName, 'Álcool') || strpos($produtoName, 'álcool') ||  strpos($produtoName, 'ÁLCOOL')) :
                        $blockTransporte = true;
                        continue;
                    endif;
                endforeach;
            endif;

            if (!empty($produto->getPlanoId())):
                $produtoKit = $variacao->getProdutoNomeCompleto();
                
                foreach ($this->getPedidoItemsAll($produto->getPlanoId()) as $objPedidoItem):
               
                    $produto =  strtolower($objPedidoItem->getProdutoVariacao()->getProduto()->getNome());
                    if(strpos($produto, 'alcool') || strpos($produto, 'Álcool') || strpos($produto, 'álcool')) {
                        $blockTransporte = true;
                    }
                endforeach;

            else:
                $produto =  strtolower($pedidoItem->getProdutoVariacao()->getProduto()->getNome());
                if(strpos($produto, 'alcool') || strpos($produto, 'Álcool') || strpos($produto, 'álcool')) {
                    $blockTransporte = true;
                }
            endif;

        endforeach;

        return $blockTransporte;
    }

    public function getBlockItemTotransporteTG() {

        foreach($this->getPedidoItems() as $pedidoItem) :

            $blockTransporte = false;
            $cep = str_replace("-","",$this->getEndereco()->getCep());

            $requestData= [ 'dominio' => 'TGT',
                'login' => 'spigreen',
                'senha' => '123',
                'cnpjPagador' => '31716218000332',
                'cepOrigem' => '29161382',
                'cepDestino' => $cep,
                'valorNF' => $pedidoItem->getValorTotal() ,
                'quantidade' => $pedidoItem->getQuantidade(),
                'peso' => $pedidoItem->getPeso(),
                'volume' => '0' ,
                'mercadoria' => '1',
                'cnpjDestinatario' => 'N',
                'entDificil' => 'N',
                'destContribuinte' => 'N'
            ];

            $urlWsdl = "https://ssw.inf.br/ws/sswCotacao/index.php?wsdl";

            $soapClient = new \SoapClient($urlWsdl, [
                'exceptions' => true
            ]);

            $retorno = $soapClient->__soapCall("cotar", $requestData);
            $retornoXML = new \SimpleXMLElement($retorno);

            if($retornoXML->erro == -1){
                $blockTransporte = true;
            }

            return $blockTransporte;
        endforeach;

    }
}
