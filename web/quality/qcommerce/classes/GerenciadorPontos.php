<?php

use Monolog\Logger;

/**
 * Description of GerenciadorPontos
 *
 * @author André Garlini
 */
class GerenciadorPontos
{
    const PORCENTAGEM_BONUS_INDICACAO_INDIRETA = 3; //todo: precisa ser configuravel?

    /**
     *
     * @var PropelPDO
     */
    protected $con;

    /** @var Logger monolog */
    private $logger;

    /**
     * GerenciadorPontos constructor.
     * @param PropelPDO $con
     * @param Logger $logger
     */
    public function __construct(PropelPDO $con, Logger $logger)
    {
        $this->con = $con;
        $this->logger = $logger;
    }

    /**
     * Distribute points for a specific order.
     *
     * @param Pedido $pedido
     * @throws Exception
     */ 
    public function distribuiPontosPedido(Pedido $pedido): void
    {
        if ($pedido->isCancelado()) :
            $this->logger->info('Pedido cancelado: ' . $pedido->getId());

            return;
        endif;

        $cliente = $pedido->getCliente($this->con);

        if (!$cliente instanceof Cliente) :
            return;
        endif;

        $this->con->beginTransaction();

        try {
            /*$percentualDistribuidor = trim(Config::get('bonificacao.distribuidor_bonus'));

            if (($hotsite = $pedido->getHotsite($this->con))
                && ($franqueado = $hotsite->getCliente($this->con))
                && ($franqueado->getId() != $cliente->getId())
                && (!empty($percentualDistribuidor) && $percentualDistribuidor > 0)) :
                //distribui os pontos para o cliente do franqueado
                $this->distribuiPontosFranqueado($pedido, $franqueado, $percentualDistribuidor);
            endif;*/

            if (!$cliente->isInTree()) :
                $this->con->commit();
                return;
            endif;

            if ($pedido->isKitAdesaoPedido($this->con)) :
                $this->distribuiBonusIndicacao($pedido);
            endif;

            // Sempre verifica se há produtos que não são kit em todos os pedidos
            // para registrar o extrato de recompra, mesmo que seja realizado junto com kit de ativação
            $this->distribuiBonusResidual($pedido);

            //$this->distribuiBonusIndicacaoIndireta($pedido);
            //$this->calculaPontosDistribuirRedeBinaria($pedido);

            //Se distribuimos pontos de compra de plano, temos que salvar este total de pontos no cliente
            // (será necessário em um eventual upgrade de plano).
            $totalPontosKits = $this->getTotalPontosKitsDoPedido($pedido);

            if ($totalPontosKits > 0) :
                $pedido->getCliente($this->con)->setPontosDistribuidosPlanos($totalPontosKits);
                $pedido->getCliente($this->con)->save($this->con);
            endif;

            $this->con->commit();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());

            if ($this->con->isInTransaction()) :
                $this->con->rollBack();
            endif;
        }
    }

    /**
     * @param Cliente $cliente
     * @param DateTime|null $inicio
     * @param DateTime|null $fim
     * @return float
     * @throws PropelException
     */
    public function getTotalValores(Cliente $cliente, DateTime $inicio = null, DateTime $fim = null)
    {
        $query = ExtratoQuery::create()
            ->joinPedido(null, Criteria::LEFT_JOIN)
            ->filterByCliente($cliente)
            ->addAsColumn('total', sprintf("SUM(%s)", PedidoPeer::VALOR_ITENS))
            ->filterByBloqueado(false);

        if ($inicio) :
            $inicio = clone $inicio;
            $inicio->setTime(0, 0, 0);
            $query->filterByData($inicio, Criteria::GREATER_EQUAL);
        endif;

        if ($fim) :
            $fim = clone $fim;
            $fim->setTime(23, 59, 59);
            $query->filterByData($fim, Criteria::LESS_EQUAL);
        endif;

        if ($row = BasePeer::doSelect($query)->fetch()) :
            return (float)$row['total'];
        endif;

        return 0.0;
    }

    /**
     * @param Cliente $cliente
     * @param DateTime|null $inicio
     * @param DateTime|null $fim
     * @param string|null $filter
     * @return float
     * @throws PropelException
     */
    public function getTotalPontosDisponiveisParaResgate(Cliente $cliente, DateTime $inicio = null, DateTime $fim = null, $filter = null, $comResgatePendente = false)
    {
        $query = ExtratoQuery::create()
            ->filterByCliente($cliente)
            ->_if($filter == Extrato::TIPO_INDICACAO_DIRETA)
                ->filterByTipo([
                    Extrato::TIPO_INDICACAO_DIRETA,
                    Extrato::TIPO_TRANSFERENCIA,
                    Extrato::TIPO_RESGATE,
                    Extrato::TIPO_PAGAMENTO_PEDIDO,
                    Extrato::TIPO_PAGAMENTO_PARCIAL_PEDIDO,
                    Extrato::TIPO_DISTRIBUICAO_REDE,
                    Extrato::TIPO_VENDA_HOTSITE,
                    Extrato::TIPO_PARTICIPACAO_RESULTADOS,
                    Extrato::TIPO_BONUS_ACELERACAO,
                    Extrato::TIPO_BONUS_DESEMPENHO,
                    Extrato::TIPO_BONUS_DESTAQUE
                ])
            ->_elseif($filter == Extrato::TIPO_INDICACAO_INDIRETA)
                ->filterByTipo(Extrato::TIPO_INDICACAO_INDIRETA)
            ->_elseif($filter == Extrato::TIPO_RESIDUAL)
                ->filterByTipo(Extrato::TIPO_RESIDUAL)
            ->_endif()
            ->addAsColumn('total', sprintf(
                "SUM(IF(%s = '-', %s * -1, %s))",
                ExtratoPeer::OPERACAO,
                ExtratoPeer::PONTOS,
                ExtratoPeer::PONTOS
            ));

        if ($inicio) :
            $inicio = clone $inicio;
            $inicio->setTime(0, 0, 0);
            $query->filterByData($inicio, Criteria::GREATER_EQUAL);
        endif;

        if ($fim) :
            $fim = clone $fim;
            $fim->setTime(23, 59, 59);
            $query->filterByData($fim, Criteria::LESS_EQUAL);
        endif;

        $total = 0;

        if ($row = BasePeer::doSelect($query)->fetch()) :
            $total = (float) $row['total'];
        endif;

        if ($filter == Extrato::TIPO_INDICACAO_DIRETA && $comResgatePendente) :
            $valorResgate = ResgateQuery::create()
                ->select(['total'])
                ->addAsColumn('total', sprintf('SUM(%s)', ResgatePeer::VALOR))
                ->filterByCliente($cliente)
                ->filterBySituacao(Resgate::SITUACAO_PENDENTE)
                ->findOne();

            $total -= (float) $valorResgate ?? 0;
        endif;

        return $total;
    }

    /**
     * @param Cliente $cliente
     * @return float
     * @throws PropelException
     */

    public function getTotalPontosReservadosComResgate(Cliente $cliente)
    {
        $query = ResgateQuery::create()
            ->filterBySituacao(Resgate::SITUACAO_PENDENTE)
            ->addAsColumn('total', "SUM(VALOR)")
            ->filterByCliente($cliente);

        if ($row = BasePeer::doSelect($query)->fetch()) {
            return (float)$row['total'];
        }

        return 0.0;
    }

    /**
     * Tenta efetuar o resgate de pontos passado como argumento.
     * Caso o cliente não possua pontos suficientes, null é retornado.
     *
     * @param Resgate $resgate
     * @return Extrato|null O extrato gerado referente ao resgate realizado ou null caso o saldo de pontos do cliente seja insuficiente.
     */
    public function tentaEfetuarResgate(Resgate $resgate)
    {

        $totalPontosDisponiveis = $this->getTotalPontosDisponiveisParaResgate($resgate->getCliente());

        if ($resgate->getValorDepositar() > $totalPontosDisponiveis) :
            return null;
        endif;

        return $this->doEfetuaResgate($resgate);
    }


    /**
     * Efetua o resgate de pontos passado como argumento.
     *
     * @param Resgate $resgate O extrato gerado referente ao resgate realizado.
     *
     * @return Extrato|null O extrato gerado referente ao resgate realizado.
     * @throws RuntimeException Quando o saldo de pontos do cliente for insuficiente.
     */
    public function efetuaResgate(Resgate $resgate)
    {
        if ($resgate->getValor() > $this->getTotalPontosDisponiveisParaResgate($resgate->getCliente())) :
            throw new RuntimeException('O saldo de pontos deste cliente é insuficiente.');
        endif;

        return $this->doEfetuaResgate($resgate);
    }


    /**
     * Efetua o resgate de pontos.
     * Atenção: esta função não verifica o saldo disponivel. É dever do chamador realizar a verificação.
     *
     * @param Resgate $resgate
     * @return \Extrato O extrato gerado referente ao resgate realizado.
     */
    protected function doEfetuaResgate(Resgate $resgate)
    {
        $con = $this->con;
        $extrato = new Extrato();
        $extratoTaxa = new Extrato();
        $con->beginTransaction();

        try {
            $extrato->setResgate($resgate);
            $extrato->setCliente($resgate->getCliente($con));
            $extrato->setTipo(Extrato::TIPO_RESGATE);
            $extrato->setPontos($resgate->getValorDepositar());
            $extrato->setOperacao('-');
            $extrato->setObservacao(Config::get('resgate.texto_extrato'));
            $extrato->save($con);

            $taxa = $resgate->getValorTaxa();

            if (!empty($taxa)) :
                $extratoTaxa->setResgate($resgate);
                $extratoTaxa->setCliente($resgate->getCliente($con));
                $extratoTaxa->setTipo(Extrato::TIPO_RESGATE);
                $extratoTaxa->setPontos($taxa);
                $extratoTaxa->setOperacao('-');
                $extratoTaxa->setObservacao(Config::get('resgate.texto_taxa_extrato'));
                $extratoTaxa->save($con);
            endif;

            $resgate->setSituacao(Resgate::SITUACAO_EFETUADO);
            $resgate->save($con);

            $con->commit();
        } catch (Exception $ex) {
            if ($con->isInTransaction()) :
                $con->rollBack();
            endif;

            throw $ex;
        }

        return $extrato;
    }

    /**
     * Cancela o resgate passado como argumento (situacao = NAOEFETUADO).
     * Qualquer extrato gerado referente a este resgate será removido.
     *
     * @param Resgate $resgate
     * @return int Total de extratos removidos.
     * @throws Exception
     */
    public function cancelaResgate(Resgate $resgate)
    {
        $totalRemovidos = 0;
        $con = $this->con;
        $con->beginTransaction();

        try {
            $resgate->setSituacao(Resgate::SITUACAO_NAOEFETUADO);
            $resgate->save($con);
            $totalRemovidos = ExtratoQuery::create()
                ->filterByResgate($resgate)
                ->delete($con);

            $con->commit();
        } catch (Exception $ex) {
            if ($con->isInTransaction()) :
                $con->rollBack();
            endif;

            throw $ex;
        }

        return $totalRemovidos;
    }

    /**
     * Marca o resgate passado como argumento como pendente (situacao = PENDENTE).
     * Qualquer extrato gerado referente a este resgate será removido.
     *
     * @param Resgate $resgate
     * @return int Total de extratos removidos.
     * @throws Exception
     */
    public function marcaResgateComoPendente(Resgate $resgate)
    {
        $totalRemovidos = 0;
        $con = $this->con;
        $con->beginTransaction();

        try {
            $resgate->setSituacao(Resgate::SITUACAO_PENDENTE);
            $resgate->save($con);

            $totalRemovidos = ExtratoQuery::create()
                ->filterByResgate($resgate)
                ->delete($con);

            $con->commit();
        } catch (Exception $ex) {
            if ($con->isInTransaction()) {
                $con->rollBack();
            }
            throw $ex;
        }

        return $totalRemovidos;
    }

    /**
     * @param $remitente Cliente
     * @param $destinatario Cliente
     * @param $pontos double
     * @param $transferenciaId int|null
     * @throws PropelException
     */
    public function transferirPontos($remitente, $destinatario, $pontos, $transferenciaId = null)
    {
        $con = $this->con;

        $obsR = "Transferência de bônus ao usuario {$destinatario->getNomeCompleto()} ({$destinatario->getEmail()})";
        $obsD = "Transferência de bônus do usuario {$remitente->getNomeCompleto()} ({$remitente->getEmail()})";

        ExtratoPeer::geraExtrato(
            Extrato::TIPO_TRANSFERENCIA,
            '-',
            $pontos,
            $remitente->getId(),
            $obsR,
            null,
            null,
            $transferenciaId
        );

        ExtratoPeer::geraExtrato(
            Extrato::TIPO_TRANSFERENCIA,
            '+',
            $pontos,
            $destinatario->getId(),
            $obsD,
            null,
            null,
            $transferenciaId
        );
    }

    public function adicionarPontos($destinatario, $pontos)
    {
        $con = $this->con;


        $extrato = new Extrato();
        $extrato->setCliente($destinatario);
        $extrato->setTipo(Extrato::TIPO_SISTEMA);
        $extrato->setOperacao('+');
        $extrato->setPontos($pontos);
        $extrato->setPedido(null);
        $extrato->setObservacao('Pontos adicionados pelo admin');
        $extrato->save($con);

        return $extrato;
    }

    public function diminuirPontos($destinatario, $pontos)
    {
        $con = $this->con;


        $extrato = new Extrato();
        $extrato->setCliente($destinatario);
        $extrato->setTipo(Extrato::TIPO_SISTEMA);
        $extrato->setOperacao('-');
        $extrato->setPontos($pontos);
        $extrato->setPedido(null);
        $extrato->setObservacao('Pontos diminuidos pelo admin');
        $extrato->save($con);

        return $extrato;
    }

    /**
     * Cria um extrato referente ao pagamento do pedido passado como argumento.
     *
     * @param Pedido $pedido Pedido sendo pago.
     * @param float $pontos Quantidade de pontos do extrato.
     * @param bool $pagamentoParcial Indica se o pagamento do pedido é parcial.
     * @return \Extrato Extrato criado.
     */
    public function criaExtratoParaPagamentoDePedido(Pedido $pedido, $pontos, $pagamentoParcial = false)
    {
        $con = $this->con;

        $extrato = new Extrato();
        $extrato->setCliente($pedido->getCliente($con));
        $extrato->setTipo($pagamentoParcial ? Extrato::TIPO_PAGAMENTO_PARCIAL_PEDIDO : Extrato::TIPO_PAGAMENTO_PEDIDO);
        $extrato->setOperacao('-');
        $extrato->setPontos($pontos);
        $extrato->setPedido($pedido);
        if ($pagamentoParcial) {
            $extrato->setObservacao(sprintf('Pagamento parcial do pedido %s.', $pedido->getId()));
        } else {
            $extrato->setObservacao(sprintf('Pagamento do pedido %s.', $pedido->getId()));
        }
        $extrato->save($con);

        return $extrato;
    }

    /**
     * Expira os pontos do cliente passado como argumento.
     *
     * @param Cliente|int $cliente Cliente ou um id de cliente.
     * @param DateTime|null $dataAtual Data a ser considerada como data atual (parametrizada para facilicar os testes).
     * @return array Array com os extratos de expiração que foram criados.
     * @throws Exception
     */
    public function expiraPontosCliente($cliente, DateTime $dataAtual = null)
    {
        $ret = array();

        $con = $this->con;
        $con->beginTransaction();
        try {
            if (!$cliente instanceof Cliente) {
                $cliente = ClienteQuery::create()->findPk($cliente, $con);
                if (!$cliente) {
                    throw new RuntimeException('Cliente não encontrado.');
                }
            }

            $ret = $this->doExpiraPontosCliente($cliente, $dataAtual);

            $con->commit();
        } catch (Exception $ex) {
            if ($con->isInTransaction()) {
                $con->rollBack();
            }
            throw $ex;
        }

        return $ret;
    }


    /**
     *
     * @param Cliente $cliente
     * @param DateTime|null $dataAtual
     * @return array
     */
    protected function doExpiraPontosCliente(Cliente $cliente, DateTime $dataAtual = null)
    {
        /***
         * OBSERVAÇÃO: a lógica foi copiada da Rede Facil. Se for encontrado algum bug, é provavel que tenha arrumar lá também.
         *
         */

        $ret = array();

        if (null === $dataAtual) {
            $dataAtual = new Datetime();
        }

        $con = $this->con;

        // Busca todos os pontos recebidos pelo cliente e organiza em um array por data.
        $arrPontos = array();

        $query = ExtratoQuery::create()
            ->filterByCliente($cliente)
            ->filterByOperacao('+')
            ->filterByBloqueado(false)
            ->orderByData(Criteria::ASC);

        foreach ($query->find($con) as $extrato) {
            /* @var $extrato Extrato */
            if ($extrato->getPontos() <= 0) {
                continue;
            }

            $strDataExtrato = $extrato->getData('Y-m-d');

            if (isset($arrPontos[$strDataExtrato])) {
                $arrPontos[$strDataExtrato] = array(
                    'DATA-EXPIRACAO' => $extrato->getDataExpiracao('Y-m-d'),
                    'PONTOS' => $arrPontos[$strDataExtrato]['PONTOS'] + $extrato->getPontos()
                );
            } else {
                $arrPontos[$strDataExtrato] = array(
                    'DATA-EXPIRACAO' => $extrato->getDataExpiracao('Y-m-d'),
                    'PONTOS' => $extrato->getPontos()
                );
            }
        }

        // Cria indice numérico para o array
        $indices = array();
        foreach ($arrPontos as $data => $arrDetalhes) {
            $indices[] = $data;
        }

        // Busca todos os débitos (uso dos pontos ou expirações lançadas) e vou retirando dos pontos mais antigos aos mais novos
        $query = ExtratoQuery::create()
            ->filterByCliente($cliente)
            ->filterByOperacao('-')
            ->filterByBloqueado(false)
            ->orderByData(Criteria::ASC);

        foreach ($query->find($con) as $extrato) {
            if ($extrato->getPontos() <= 0) {
                continue;
            }

            $i = 0;
            $pontosExtrair = $extrato->getPontos();

            while ($pontosExtrair > 0 && $i < count($indices)) {
                if ($arrPontos[$indices[$i]]['PONTOS'] > $pontosExtrair) {
                    $arrPontos[$indices[$i]]['PONTOS'] = $arrPontos[$indices[$i]]['PONTOS'] - $pontosExtrair;
                    $pontosExtrair = 0;
                } else {
                    $pontosExtrair = $pontosExtrair - $arrPontos[$indices[$i]]['PONTOS'];
                    $arrPontos[$indices[$i]]['PONTOS'] = 0;
                }
                $i++;
            }
        }

        // Limpo o array de pontos retirando as entradas que foram zeradas
        $arrPontosFinal = array();
        foreach ($arrPontos as $data => $arrDetalhes) {
            if ($arrDetalhes['PONTOS'] > 0) {
                $arrPontosFinal[$data] = $arrDetalhes;
            }
        }

        foreach ($arrPontosFinal as $data => $arrDetalhes) {
            $dtExpiracao = new Datetime($arrDetalhes['DATA-EXPIRACAO']);

            // Se a data atual for maior que a data de expiração lança a expiração no extrato
            if ($dataAtual > $dtExpiracao) {
                $extrato = new Extrato();
                $extrato->setCliente($cliente);
                $extrato->setTipo(Extrato::TIPO_SISTEMA);
                $extrato->setOperacao('-');
                $extrato->setPontos($arrDetalhes['PONTOS']);
                $extrato->setObservacao(sprintf('Pontos expirados data %s.', $dtExpiracao->format('d/m/Y')));
                $extrato->save($con);
                $ret[] = $extrato;
            }
        }

        return $ret;
    }

    /**
     * @param Pedido $pedido
     * @return int
     * @throws PropelException
     */
    protected function getTotalPontosKitsDoPedido(Pedido $pedido)
    {
        $totalPontosKits = 0;

        //soma o total de pontos
        foreach ($pedido->getPedidoItems(null, $this->con) as $pedidoItem) {
            /* @var $pedidoItem PedidoItem */
            $produto = $pedidoItem->getProdutoVariacao()->getProduto();

            if (!$produto->isKitAdesao()) :
                continue; //apenas kits
            endif;

            $totalPontosKits += $pedidoItem->getValorPontosUnitario() * $pedidoItem->getQuantidade();
        }

        return $totalPontosKits;
    }

    /**
     * @param Pedido $pedido
     * @throws PropelException
     */
    protected function distribuiBonusIndicacao(Pedido $pedido)
    {
        $con = $this->con;
        $cliente = $pedido->getCliente($con);
        $patrocinador = $cliente->getPatrocinador($con);
        $direta = true;

        // TODO: create test for this condition.
        if (!$patrocinador) :
            if ($cliente->isRoot()) :
                return;
            else :
                $this->logger->info('O cliente deste pedido não possui um patrocinador: ' .  $pedido->getId());
            endif;
        endif;

        // TODO: create test for this condition.
        $query = ExtratoQuery::create()
            ->filterByPedido($pedido)
            ->filterByTipo(array(Extrato::TIPO_INDICACAO_INDIRETA, Extrato::TIPO_INDICACAO_DIRETA));

        if ($query->count($con) > 0) :
            return;
        endif;

        $totalPontosKits = $this->getTotalPontosKitsDoPedido($pedido);
        $porcentagens = json_decode(Config::get('bonificacao.indicacao_bonus'), true);
        $levels = count($porcentagens);
        $level = 1;

        // Cria extrato de compra pessoal
        $extrato = new Extrato();
        $extrato->setTipo(Extrato::TIPO_INDICACAO_DIRETA);
        $extrato->setOperacao('+');
        $extrato->setPontos(0);
        $extrato->setCliente($cliente);
        $extrato->setPedido($pedido);
        $extrato->setData($pedido->getCreatedAt());
        $extrato->setObservacao('Pontos de ativação pessoal. Pedido '. $pedido->getId());
        $extrato->save($con);

        while ($patrocinador instanceof Cliente) :
            $preActive = false;

            if ($patrocinador->getNaoCompra()) :
                $preActive = true;
            endif;

            if ($level <= $levels) :
                $pontosDistribuir = ($totalPontosKits * $porcentagens[$level++]) / 100;
                $extrato = new Extrato();

                if ($direta) :
                    $extrato->setTipo(Extrato::TIPO_INDICACAO_DIRETA);
                else :
                    $extrato->setTipo(Extrato::TIPO_INDICACAO_INDIRETA);
                endif;

                $extrato->setOperacao('+');
                $extrato->setPontos($pontosDistribuir);
                $extrato->setCliente($patrocinador);
                $extrato->setPedido($pedido);
                $extrato->setData($pedido->getCreatedAt());

                if ($direta) :
                    $extrato->setObservacao(sprintf('Bônus de Equipe Direta. Pedido %d - Cliente ' .
                        $pedido->getCliente()->getNomeCompleto(), $pedido->getId()));
                else :
                    $extrato->setObservacao(sprintf('Bônus de Equipe Indireta. Pedido %d - Cliente ' .
                        $pedido->getCliente()->getNomeCompleto(), $pedido->getId()));
                endif;

                $extrato->save($con);

                if ($preActive) :
                    $extratoDesconto = $extrato->copy();
                    $extratoDesconto->setOperacao('-');
                    $extratoDesconto->setObservacao(sprintf('Retirada valor da indicação do pedido %d. Motivo: ' .
                        ConfiguracaoPontuacaoMensalPeer::getDescricaoExtrato() . '.', $pedido->getId()));
                    $extratoDesconto->save($con);
                endif;
            endif;

            $patrocinador = $patrocinador->getPatrocinador($con);
            $direta = false;
        endwhile;
    }

    /**
     * Regras Bonus Indicacao Indireta:
     * Paga no momento da confirmação de pagamento apenas nas compras de Kits;
     * Paga 30% dos pontos dos produtos comprados, divididos entre os 10 níveis acima do comprador, 3% para cada um.
     *
     * @param Pedido $pedido
     * @throws PropelException
     */
//    protected function distribuiBonusIndicacaoIndireta(Pedido $pedido)
//    {
//        $porcentagem = self::PORCENTAGEM_BONUS_INDICACAO_INDIRETA;
//        $con = $this->con;
//        // verifica se ja foi gerado os pontos para este pedido
//        $query = ExtratoQuery::create()
//            ->filterByPedido($pedido)
//            ->filterByTipo(Extrato::TIPO_INDICACAO_INDIRETA);
//
//        if ($query->count($con) > 0) :
//            return; // pontos ja foram gerados
//        endif;
//
//        $totalPontosKits = $this->getTotalPontosKitsDoPedido($pedido);
//
//        //Se for um upgrade de plano, temos que descontar o que ja foi considerado na distribuição do plano anterior.
//        if ($pontosPlanoAnterior = $pedido->getCliente($con)->getPontosDistribuidosPlanos()) :
//            //$totalPontosKits -= $pontosPlanoAnterior;
//        endif;
//
//        if ($totalPontosKits <= 0) :
//            return;
//        endif;
//
//        $cliente = $pedido->getCliente($con);
//        //Patrocinador que recebeu o bonus de indicacao direta
//        $patrocinadorBonusDireto = ($patrocinadorDireto = $cliente->getPatrocinadorDireto($con)) ? $patrocinadorDireto : $cliente->getPatrocinador($con);
//        $patrocinador = $cliente->getPatrocinadorDireto($con);
//        $porcentagens = array(10, 5, 5, 5);
//        $qtdNiveis = count($porcentagens);
//
//        for ($i = 0; $i < $qtdNiveis; $i++) :
//            if (!$patrocinador) :
//                break;
//            endif;
//
//            if ($patrocinadorBonusDireto && $patrocinadorBonusDireto->getId() == $patrocinador->getId()) :
//                $patrocinador = $patrocinador->getPatrocinadorDireto($con);
//                $i--; //ignora este loop
//                continue; //este patrocinador recebeu bonus de indicacao direta. Portanto, não deve receber bonus indicacao indireta.
//            endif;
//
//            if ($patrocinador->getVago()) :
//                $patrocinador = $patrocinador->getPatrocinadorDireto($con);
//                $i--; //ignora este loop
//                continue; //este cadastro está vago. Portanto, não deve receber bonus indicacao indireta.
//            endif;
//
//            if (!$patrocinador->getPlano() || !$patrocinador->getPlano()->getIndicacaoIndireta()) :
//                $patrocinador = $patrocinador->getPatrocinadorDireto($con);
//                continue; //O plano do patrocinador não permite bonus de indicacao indireta.
//            endif;
//
//            // Patrocinador Direto no pré cadastro lança crédito e Débito
//
//            $preActive = false;
//
//            if ($patrocinador->getNaoCompra()) :
//                $preActive = true;
//            endif;
//
//            $pontosDistribuir = ($totalPontosKits * $porcentagens[$i]) / 100;
//            $extrato = new Extrato();
//            $extrato->setTipo(Extrato::TIPO_INDICACAO_INDIRETA);
//            $extrato->setOperacao('+');
//            $extrato->setPontos($pontosDistribuir);
//            $extrato->setCliente($patrocinador);
//            $extrato->setPedido($pedido);
//            $extrato->setData(new DateTime());
//            $extrato->setObservacao(sprintf('Indicação Indireta. Pedido %d - Cliente ' . $pedido->getCliente()->getNomeCompleto(), $pedido->getId()));
//            $extrato->save($con);
//
//            if ($preActive) :
//                $extratoDesconto = $extrato->copy();
//                $extratoDesconto->setOperacao('-');
//                $extratoDesconto->setObservacao(sprintf('Retirada valor da indicação direta do pedido %d. Motivo: ' . ConfiguracaoPontuacaoMensalPeer::getDescricaoExtrato() . '.', $pedido->getId()));
//                $extratoDesconto->save($con);
//            endif;
//
//            //vai subindo na rede
//            $patrocinador = $patrocinador->getPatrocinadorDireto($con);
//        endfor;
//    }

    /**
     * Add points directly for order.
     *
     * @param Pedido $pedido
     * @throws PropelException
     */
    public function adicionaBonusIndicacaoDireta(Pedido $pedido)
    {
        $con = $this->con;
        //verifica se ja foi gerado os pontos para este pedido
        $query = ExtratoQuery::create()
            ->filterByPedido($pedido)
            ->filterByTipo(Extrato::TIPO_INDICACAO_DIRETA);

        if ($query->count($con) > 0) :
            return; // pontos ja foram gerados
        endif;

        $totalPontosKits = $this->getTotalPontosKitsDoPedido($pedido);

        if ($totalPontosKits <= 0) :
            return; // no kits were bought
        endif;

        $extrato = new Extrato();
        $extrato->setTipo(Extrato::TIPO_INDICACAO_DIRETA);
        $extrato->setOperacao('+');
        $extrato->setPontos($totalPontosKits);
        $extrato->setCliente($pedido->getCliente());
        $extrato->setPedido($pedido);
        $extrato->setData(new DateTime());
        $extrato->setObservacao(sprintf('Indicação Direta. Pedido %d - Cliente ' . $pedido->getCliente()->getNomeCompleto(), $pedido->getId()));
        $extrato->save($con);
    }

    /**
     * Regras Bonus Redidual:
     * Paga no momento da confirmação de pagamento, válido para produtos que não são Kits;
     *
     * Distribute points to individual.
     *
     * @param Pedido $pedido
     * @throws PropelException
     */
    protected function distribuiBonusResidual(Pedido $pedido)
    {
        $con = $this->con;

        // verifica se ja foi gerado os pontos para este pedido
        $query = ExtratoQuery::create()
            ->filterByPedido($pedido)
            ->filterByTipo(Extrato::TIPO_RESIDUAL);

        if ($query->count($con) > 0) :
            return; //pontos ja foram gerados
        endif;

        $cliente = $pedido->getCliente($con);
        $totalPontosProdutos = 0;

        foreach ($pedido->getPedidoItems(null, $con) as $pedidoItem) :
            /* @var $pedidoItem PedidoItem */

            if ($pedidoItem->getPlanoId()) :
                continue; //Produtos iniciais de planos não devem distribuir bonus residual.
            endif;

            $produto = $pedidoItem->getProdutoVariacao()->getProduto();

            if ($produto->isKitAdesao()) :
                continue; //apenas produtos normais
            endif;

            $totalPontosProdutos += $produto->getValorPontos() * $pedidoItem->getQuantidade();
        endforeach;

        if ($totalPontosProdutos <= 0) :
            return;
        endif;

        // Cria extrato de compra pessoal
        $extrato = new Extrato();
        $extrato->setTipo(Extrato::TIPO_RESIDUAL);
        $extrato->setOperacao('+');
        $extrato->setPontos(0);
        $extrato->setCliente($cliente);
        $extrato->setPedido($pedido);
        $extrato->setData($pedido->getCreatedAt());
        $extrato->setObservacao('Pontos de recompra pessoal. Pedido '. $pedido->getId());
        $extrato->save($con);

        $patrocinador = $cliente->getPatrocinador($con);
        $nivel = 1;

        while ($patrocinador) :
            // Não bonifica o cliente se o plano dele não permite bonificação de recompra
            // Mesmo assim não ignora o nível verificado
            if (!$patrocinador->getPlano() || !$patrocinador->getPlano()->getResidual()) :
                $patrocinador = $patrocinador->getPatrocinador($con);
                $nivel++;
                continue;
            endif;

            // Se o cadastro do cliente for vago, não incremente o nível e passa para o próximo nível
            if ($patrocinador->getVago()) :
                $patrocinador = $patrocinador->getPatrocinador($con);
                continue;
            endif;

            $bonusRecompra = 0;
            $gerenciador = new GerenciadorPlanoCarreira($con, $patrocinador);

            $qualificacao = $gerenciador->getQualificacaoAtualHistorico(Date('m'), Date('Y'));
            $qualificacao = $qualificacao ? $qualificacao->getPlanoCarreiraId() : $qualificacao;

            switch ($nivel) :
                case 1:
                    if ($qualificacao >= 1) :
                        $bonusRecompra = $totalPontosProdutos * 0.1;
                    endif;
                    break;
                case 2:
                    if ($qualificacao >= 1) :
                        $bonusRecompra = $totalPontosProdutos * 0.05;
                    endif;
                    break;
                case 3:
                    if ($qualificacao >= 3) :
                        $bonusRecompra = $totalPontosProdutos * 0.03;
                    endif;
                    break;
                case 4:
                    if ($qualificacao >= 5) :
                        $bonusRecompra = $totalPontosProdutos * 0.03;
                    endif;
                    break;
                case 5:
                    if ($qualificacao >= 6) :
                        $bonusRecompra = $totalPontosProdutos * 0.03;
                    endif;
                    break;
                case 6:
                    if ($qualificacao >= 9) :
                        $bonusRecompra = $totalPontosProdutos * 0.03;
                    endif;
                    break;
                case 7:
                    if ($qualificacao >= 10) :
                        $bonusRecompra = $totalPontosProdutos * 0.04;
                    endif;
                    break;
                case 8:
                    if ($qualificacao >= 13) :
                        $bonusRecompra = $totalPontosProdutos * 0.04;
                    endif;
                    break;
            endswitch;

            $extrato = new Extrato();
            $extrato->setTipo(Extrato::TIPO_RESIDUAL);
            $extrato->setOperacao('+');
            $extrato->setPontos($bonusRecompra);
            $extrato->setCliente($patrocinador);
            $extrato->setPedido($pedido);
            $extrato->setData(new DateTime());
            $extrato->setObservacao(sprintf('Bônus Recompra. Pedido %d - Cliente ' . $pedido->getCliente()->getNomeCompleto(), $pedido->getId()));
            $extrato->save($con);

            // Patrocinador sem direito de recompra lança crédito e Débito
            $recebeRecompra = false;

            if ($patrocinador->getNaoCompra()) :
                $recebeRecompra = true;
            endif;

            if ($recebeRecompra) :
                $extratoDesconto = $extrato->copy();
                $extratoDesconto->setOperacao('-');
                $extratoDesconto->setObservacao(sprintf('Retirada valor da recompra do pedido %d. Motivo: ' . ConfiguracaoPontuacaoMensalPeer::getDescricaoExtrato() . '.', $pedido->getId()));
                $extratoDesconto->save($con);
            endif;

            $nivel++;
            $patrocinador = $patrocinador->getPatrocinador($con);
        endwhile;
    }

    /**
     * @param Pedido $pedido
     * @param Cliente $cliente
     * @param $percentualDesconto
     * @throws PropelException
     *
     */
    protected function distribuiPontosFranqueado(Pedido $pedido, Cliente $cliente, $percentualDesconto)
    {
        $con = $this->con;

        //verifica se ja foi gerado os pontos para este pedido
        $query = ExtratoQuery::create()
            ->filterByPedido($pedido)
            ->filterByTipo(Extrato::TIPO_VENDA_DISTRIBUIDOR);

        if ($query->count($con) > 0) {
            return; //pontos ja foram gerados
        }

        if ($percentualDesconto <= 0) {
            return;
        }


        $totalPontosProdutos = 0;

        $valorItens = 0;
        foreach ($pedido->getPedidoItems(null, $con) as $pedidoItem) {
            /* @var $pedidoItem PedidoItem */

            if ($pedidoItem->getPlanoId()) {
                continue; //Produtos iniciais de planos não devem distribuir bonus residual.
            }

            $produto = $pedidoItem->getProdutoVariacao()->getProduto();

            if ($produto->isKitAdesao()) {
                continue; //apenas produtos normais
            }

            $valorItens += $produto->getValorPontos() * $pedidoItem->getQuantidade();
        }

        $pontosDistribuir = ($valorItens * $percentualDesconto) / 100;

        if ($pontosDistribuir <= 0) {
            return;
        }

        $preActive = false;

        if ($cliente->getNaoCompra()) {
            $preActive = true;
        }


        //cria o extrato
        $extrato = new Extrato();
        $extrato->setTipo(Extrato::TIPO_VENDA_DISTRIBUIDOR);
        $extrato->setOperacao('+');
        $extrato->setPontos($pontosDistribuir);
        $extrato->setCliente($cliente);
        $extrato->setPedido($pedido);
        $extrato->setData(new DateTime());
        $extrato->setObservacao(sprintf('Venda pela página de um distribuidor. Pedido %d - Cliente ' . $pedido->getCliente()->getNomeCompleto(), $pedido->getId()));

        $extrato->save($con);

        if ($preActive) {
            $extratoDesconto = $extrato->copy();
            $extratoDesconto->setOperacao('-');
            $extratoDesconto->setObservacao(sprintf('Retirada valor da venda pela página do distribuidor do pedido %d. Motivo: ' . ConfiguracaoPontuacaoMensalPeer::getDescricaoExtrato() . '.', $pedido->getId()));
            $extratoDesconto->save($con);
        }
    }

    public function getTotalPontosPlanoCarreira(Cliente $cliente, DateTime $dataInicio = null, Datetime $dataFim = null, $tipoExtrato = null) {
        $query = ExtratoQuery::create()
            ->join('Pedido')
            ->useQuery('Pedido')
            ->join('PedidoItem')
            ->endUse()
            ->useQuery('PedidoItem')
            ->join('ProdutoVariacao')
            ->endUse()
            ->useQuery('ProdutoVariacao')
            ->join('Produto')
            ->endUse()
            ->select(['TotalPontos'])
            ->_if($tipoExtrato == Extrato::TIPO_INDICACAO_DIRETA)
            ->filterByTipo('INDICACAO_DIRETA')
            ->withColumn('SUM(IF(Produto.Id IN (2, 123), Produto.ValorPontos, 0))', 'TotalPontos')
            ->_elseif($tipoExtrato == Extrato::TIPO_INDICACAO_INDIRETA)
            ->filterByTipo('INDICACAO_INDIRETA')
            ->withColumn('SUM(IF(Produto.Id IN (2, 123), Produto.ValorPontos, 0))', 'TotalPontos')
            ->_elseif($tipoExtrato == Extrato::TIPO_RESIDUAL)
            ->filterByTipo('RESIDUAL')
            ->withColumn('SUM(IF(Produto.Id NOT IN (2, 123), Produto.ValorPontos * PedidoItem.Quantidade, 0))', 'TotalPontos')
            ->_endif()
            ->filterByCliente($cliente)
            ->filterByBloqueado(false);

        if ($dataInicio) :
            $dataInicio->setTime(0, 0, 0);
            $query->filterByData($dataInicio, Criteria::GREATER_EQUAL);
        endif;

        if ($dataFim) :
            $dataFim->setTime(23, 59, 59);
            $query->filterByData($dataFim, Criteria::LESS_EQUAL);
        endif;

        return $query->find()->toArray();
    }

    /**
     * @param $pedido Pedido
     * @param $cliente Cliente
     * @param $plano Plano
     * @throws PropelException
     */
    public function geraBonusHotsite($pedido, $cliente, $plano)
    {
        $valorItens = 0;

        foreach ($pedido->getPedidoItems() as $item) :
            if (!$item->getProdutoVariacao()->getProduto()->isKitAdesao()) :
                $valorItens += $item->getValorTotal();
            endif;
        endforeach;

        $bonusHotsite = $valorItens * ($plano->getPercDescontoHotsite() ?? 0) / 100;
        $observacao = "Bônus Hotsite. Pedido: {$pedido->getId()} - Cliente {$pedido->getCliente()->getNomeCompleto()}";

        ExtratoPeer::geraExtrato(
            Extrato::TIPO_VENDA_HOTSITE,
            '+',
            round($bonusHotsite, 3),
            $cliente->getId(),
            $observacao,
            $pedido->getId()
        );
    }

    public function getTotalBonusClientePreferencial(Cliente $cliente, DateTime $dataInicial = null, DateTime $dataFinal = null)
    {
        $query = ExtratoQuery::create()
            ->select(['TotalBonus'])
            ->withColumn('COALESCE(SUM(IF(Extrato.Operacao = "-", Extrato.Pontos * -1, Extrato.Pontos)), 0)',
                'TotalBonus')
            ->filterByCliente($cliente)
            ->filterByTipo(Extrato::TIPO_CLIENTE_PREFERENCIAL)
            ->_if($dataInicial)
                ->filterByData($dataInicial, Criteria::GREATER_EQUAL)
            ->_endif()
            ->_if($dataFinal)
                ->filterByData($dataFinal, Criteria::LESS_EQUAL)
            ->_endif()
            ->filterByBloqueado(false)
            ->find();

        return $query[0];
    }

    public function getTotalBonusFrete(Cliente $cliente, DateTime $dataInicial = null, DateTime $dataFinal = null)
    {
        $query = ExtratoQuery::create()
            ->select(['TotalBonus'])
            ->withColumn('COALESCE(SUM(IF(Extrato.Operacao = "-", Extrato.Pontos * -1, Extrato.Pontos)), 0)',
                'TotalBonus')
            ->filterByCliente($cliente)
            ->filterByTipo(Extrato::TIPO_BONUS_FRETE)
            ->_if($dataInicial)
                ->filterByData($dataInicial, Criteria::GREATER_EQUAL)
            ->_endif()
            ->_if($dataFinal)
                ->filterByData($dataFinal, Criteria::LESS_EQUAL)
            ->_endif()
            ->filterByBloqueado(false)
            ->find();

        return $query[0];
    }

}
