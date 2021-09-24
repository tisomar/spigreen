<?php

use Monolog\Logger;

/**
 * Description of GerenciadorPontos
 *
 * @author Dionathan Córdova
 */
class GerenciadorPontosAcumulados
{
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
     * Tenta efetuar o resgate de pontos passado como argumento.
     * Caso o cliente não possua pontos suficientes, null é retornado.
     *
     * @param ResgatePremiosAcumulados $resgate
     * @return Extrato|null O extrato gerado referente ao resgate realizado ou null caso o saldo de pontos do cliente seja insuficiente.
     */
    public function tentaEfetuarResgate(ResgatePremiosAcumulados $resgate) {
        $premioSelecionado = $resgate->getSelecionado();

        if($premioSelecionado == 'DINHEIRO') :
            $totalPontosDisponiveis = $this->getTotalPontosDisponiveisParaResgate($resgate->getCliente());

            $premio = trim(str_replace('R$ ', '', $resgate->getPremio()));
            $valor = number_format(str_replace(",",".",str_replace(".","",$premio)), 2, '.', '');

            if ($premio > $totalPontosDisponiveis) :
                throw new RuntimeException('O saldo de pontos deste cliente é insuficiente.');
            endif;

            return $this->doEfetuaResgate($resgate, $valor);
        endif;

        $resgate->setSituacao(ResgatePremiosAcumulados::SITUACAO_EFETUADO);
        $resgate->save();
        return true;
    }


    /**
     * Efetua o resgate de pontos passado como argumento.
     *
     * @param ResgatePremiosAcumulados $resgate O extrato gerado referente ao resgate realizado.
     *
     * @return Extrato|null O extrato gerado referente ao resgate realizado.
     * @throws RuntimeException Quando o saldo de pontos do cliente for insuficiente.
     */
    public function efetuaResgate(ResgatePremiosAcumulados $resgate)
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
     * @param ResgatePremiosAcumulados $resgate
     * @return \Extrato O extrato gerado referente ao resgate realizado.
     */
    protected function doEfetuaResgate(ResgatePremiosAcumulados $resgate, $valor = 0)
    {
        $con = $this->con;
        $con->beginTransaction();

        try {
            if($valor > 0) :
                $extrato = new Extrato();
                $extrato->setCliente($resgate->getCliente($con));
                $extrato->setTipo(Extrato::TIPO_INDICACAO_DIRETA);
                $extrato->setPontos($valor);
                $extrato->setOperacao('+');
                $extrato->setObservacao("Pontos adquiridos pelo resgate de {$resgate->getPontosResgate()} da pontuação acumulada");
                $extrato->save($con);

                $resgate->setSituacao(ResgatePremiosAcumulados::SITUACAO_EFETUADO);
                $resgate->save($con);

                $con->commit();
            endif;
        } catch (Exception $ex) {
            if ($con->isInTransaction()) :
                $con->rollBack();
            endif;

            throw $ex;
        }

        return $extrato;
    }

    /**
     * Efetua o extorno de pontos.
     * Atenção: esta função não verifica o saldo disponivel. É dever do chamador realizar a verificação.
     *
     * @param ResgatePremiosAcumulados $resgate
     * @return \Extrato O extrato gerado referente ao resgate realizado.
     */
    public function doExtornaResgateAndExtrato(ResgatePremiosAcumulados $resgate, $valor) {
        $con = $this->con;
        $extrato = new Extrato();
        $con->beginTransaction();

        $premio = trim(str_replace('R$ ', '', $valor));
        $valor = number_format(str_replace(",",".",str_replace(".","",$premio)), 2, '.', '');

        try {
            $extrato->setCliente($resgate->getCliente($con));
            $extrato->setTipo(Extrato::TIPO_INDICACAO_DIRETA);
            $extrato->setPontos($valor);
            $extrato->setOperacao('-');
            $extrato->setObservacao("Pontos de resgate de {$resgate->getPontosResgate()} da pontuação acumulada extornados por solicitação do cliente");
            $extrato->save($con);

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
    public function cancelaResgate(ResgatePremiosAcumulados $resgate)
    {
        $totalRemovidos = 0;
        $con = $this->con;
        $con->beginTransaction();

        try {
            $resgate->setSituacao(ResgatePremiosAcumulados::SITUACAO_NAOEFETUADO);
            $resgate->save($con);

            $con->commit();
        } catch (Exception $ex) {
            if ($con->isInTransaction()) :
                $con->rollBack();
            endif;

            throw $ex;
        }

        return true;
    }

    /**
     * Marca o resgate passado como argumento como pendente (situacao = PENDENTE).
     * Qualquer extrato gerado referente a este resgate será removido.
     *
     * @param Resgate $resgate
     * @return int Total de extratos removidos.
     * @throws Exception
     */
    public function marcaResgateComoPendente(ResgatePremiosAcumulados $resgate)
    {
        $totalRemovidos = 0;
        $con = $this->con;
        $con->beginTransaction();

        try {
            $resgate->setSituacao(ResgatePremiosAcumulados::SITUACAO_PENDENTE);
            $resgate->save($con);

            $con->commit();
        } catch (Exception $ex) {
            if ($con->isInTransaction()) {
                $con->rollBack();
            }
            throw $ex;
        }

        return true;
    }

    /**
     * @param Cliente $cliente
     * @throws PropelException
     */

    public function getTotalPontosAcumuladosCliente(Cliente $cliente) {
        $gerenciador = new GerenciadorPlanoCarreira(Propel::getConnection(), $cliente);
        $clienteid = $cliente->getId();
        $totalPontos = 0;

        // todo pode ser pego o total de pontos diretamente da tabela pontos acumulados quando a proc for arrumada
        // $pontosDisponiveis = PontosAcumuladosQuery::create()->filterByClienteId($clienteid)->findOne();
        // $pontosDisponivels = (int) $pontosDisponiveis->getPontuacaoAcumuladaTotal() ?? 0;
        // return  $pontosDisponivels;
       
        // enquanto a proc não estiver pronta, a pontuacao está sendo gerada até mesmo para DIS INATIVO
        $sqlPontos = "
        SELECT 
            SUM(pi.VALOR_PONTOS_UNITARIO * pi.QUANTIDADE) TOTAL_PONTOS
        FROM qp1_pedido p,
        qp1_cliente c1,
        qp1_cliente c2,
        qp1_pedido_status_historico ph,
        qp1_pedido_item pi,
        qp1_produto_variacao pv,
        qp1_produto pr
            WHERE p.STATUS <> 'CANCELADO'
            AND p.CLIENTE_ID = c2.ID
            AND c1.tree_left <= c2.tree_left
            AND c1.tree_right >= c2.tree_right
            AND c1.ID = $clienteid 
            AND ph.PEDIDO_ID = p.ID
            AND ph.PEDIDO_STATUS_ID = 1
            AND ph.IS_CONCLUIDO = 1
            AND pi.PEDIDO_ID = p.ID
            AND pi.PLANO_ID IS NULL
            AND pi.PRODUTO_VARIACAO_ID = pv.ID
            AND pv.PRODUTO_ID = pr.ID";       
    
        $con = $this->con;
        $query = $con->prepare($sqlPontos);
        $query->execute();
    
        $totalPontos = (int) $query->fetch(PDO::FETCH_ASSOC)['TOTAL_PONTOS'] ?? 0;

        return $totalPontos;

        // conferindo se dis está ativo para receber pontos, mas trava quando a rede é muito grande
        // ----------------------- INIT --------------------------
        // $sqlPontos = "
        //     SELECT 
        //         pi.VALOR_PONTOS_UNITARIO, 
        //         pi.QUANTIDADE,
        //         p.created_at
        //     FROM qp1_pedido p,
        //     qp1_cliente c1,
        //     qp1_cliente c2,
        //     qp1_pedido_status_historico ph,
        //     qp1_pedido_item pi,
        //     qp1_produto_variacao pv,
        //     qp1_produto pr
        //         WHERE p.STATUS <> 'CANCELADO'
        //         AND p.CLIENTE_ID = c2.ID
        //         AND c1.tree_left <= c2.tree_left
        //         AND c1.tree_right >= c2.tree_right
        //         AND c1.ID = $clienteid 
        //         AND ph.PEDIDO_ID = p.ID
        //         AND ph.PEDIDO_STATUS_ID = 1
        //         AND ph.IS_CONCLUIDO = 1
        //         AND pi.PEDIDO_ID = p.ID
        //         AND pi.PLANO_ID IS NULL
        //         AND pi.PRODUTO_VARIACAO_ID = pv.ID
        //         AND pv.PRODUTO_ID = pr.ID";

        // $con = $this->con;
        // $query = $con->prepare($sqlPontos);
        // $query->execute();

        // while ($rs = $query->fetch(PDO::FETCH_OBJ)):
        //     $dateCompra = new DateTime($rs->created_at);
        //     $dateMonth = $dateCompra->format('m');
        //     $dateYaer = $dateCompra->format('Y');
        //     @$statusCliente = $gerenciador->getStatusAtivacao($dateMonth, $dateYaer);
    
        //     if($statusCliente) :
        //         $totalPontos += $rs->VALOR_PONTOS_UNITARIO * $rs->QUANTIDADE;
        //     endif;
        // endwhile;

        // return $totalPontos;
        // ----------------------- FINAL --------------------------

    }

    /**
     * @param Cliente $cliente
     * @throws PropelException
     */
    public function getPontuacaoRetiradaAdmin(Cliente $cliente) {
        $clienteid = $cliente->getId();
        
        $pontosDisponiveis = PontosAcumuladosQuery::create()->filterByClienteId($clienteid)->findOneOrCreate();
        $pontosDisponiveis->save();

        return $pontosDisponiveis->getPontuacaoRetiradaAdmin() ?? 0;
    }

    /**
     * @param Cliente $cliente
     * @throws PropelException
     */

    public function getTotalPontosDisponiveisParaResgate(Cliente $cliente) {
        $clienteid = $cliente->getId();
     
        $pontuacaoRetiradaAdmin = (int) $this->getPontuacaoRetiradaAdmin($cliente) ?? 0;
        $pontuacaoResgatada = (int) $this->getTotalPontosResgatadosResgatePremiacao($cliente) ?? 0;
        $pontosDisponivels = (int) $this->getTotalPontosAcumuladosCliente($cliente) ?? 0;
        
        return  $pontosDisponivels - $pontuacaoRetiradaAdmin - $pontuacaoResgatada;
    }

    /**
     * @param Cliente $cliente
     * @throws PropelException
     */
    public function getTotalPontosReservaResgatePremiacao(Cliente $cliente) {
        $query = ResgatePremiosAcumuladosQuery::create()
            ->filterBySituacao(ResgatePremiosAcumulados::SITUACAO_PENDENTE)
            ->addAsColumn('total', "SUM(PONTOS_RESGATE)")
            ->filterByCliente($cliente);

        if ($row = BasePeer::doSelect($query)->fetch()) {
            return (float)$row['total'];
        }

        return 0.0;
    }

    /**
     * @param Cliente $cliente
     * @throws PropelException
     */
    public function getTotalPontosResgatadosResgatePremiacao(Cliente $cliente) {
        $query = ResgatePremiosAcumuladosQuery::create()
            ->filterBySituacao(ResgatePremiosAcumulados::SITUACAO_EFETUADO)
            ->addAsColumn('total', "SUM(PONTOS_RESGATE)")
            ->filterByCliente($cliente);

        if ($row = BasePeer::doSelect($query)->fetch()) {
            return (float)$row['total'] + $this->getTotalPontosReservaResgatePremiacao($cliente);
        }

        return 0.0;
    }

    /**
     * @param  int $clienteid
     * @throws PropelException
     */

    public function getClienteRedePrimeiraGeracao($clienteid) {
        $sqlTotal = "
        SELECT 
        c2.id CLIENTE_REDE_ID,
        IF(c2.CNPJ IS NULL, c2.NOME, c2.RAZAO_SOCIAL) CLIENTE,
        c2.tree_level - c1.tree_level GERACAO
        FROM qp1_pedido p,
             qp1_cliente c1,
             qp1_cliente c2,
             qp1_pedido_status_historico ph,
             qp1_pedido_item pi,
             qp1_produto_variacao pv,
             qp1_produto pr
        WHERE p.STATUS <> 'CANCELADO'
        AND p.CLIENTE_ID = c2.ID
        AND c1.tree_left <= c2.tree_left
        AND c1.tree_right >= c2.tree_right
        AND c1.ID = $clienteid
        AND ph.PEDIDO_ID = p.ID
        AND ph.PEDIDO_STATUS_ID = 1
        AND ph.IS_CONCLUIDO = 1
        AND pi.PEDIDO_ID = p.ID
        AND pi.PLANO_ID IS NULL
        AND pi.PRODUTO_VARIACAO_ID = pv.ID
        AND pv.PRODUTO_ID = pr.ID
        AND c2.tree_level - c1.tree_level = 1
        GROUP BY c2.ID
        ORDER BY c2.ID";

        $con = $this->con;
        $query = $con->prepare($sqlTotal);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param Cliente $cliente
     * @throws PropelException
     */
    public function getPontosVME(Cliente $cliente, $pontosResgate) {
        $clienteid = $cliente->getId();
        $redePrimeiraGeracao = $this->getClienteRedePrimeiraGeracao($clienteid);

        $somaPontosRede = 0;
        foreach($redePrimeiraGeracao as $rede) :
            $objCliente = ClienteQuery::create()->filterById($rede['CLIENTE_REDE_ID'])->findOne();

            try {
                $pontoRede = $this->getTotalPontosAcumuladosCliente($objCliente);
            } catch (\Throwable $th) {
                $pontoRede = 0;
            }

            $maximoAjudaVME = ($pontosResgate * $this->percentualPontosVME($pontosResgate)) / 100;

            if($pontoRede > $maximoAjudaVME ) :
                $somaPontosRede += $maximoAjudaVME;
            else:
                $somaPontosRede += $pontoRede;
            endif;
        endforeach;

        $pontuacaoRetiradaAdmin =  (int) $this->getPontuacaoRetiradaAdmin($cliente) ?? 0;

        return $somaPontosRede - $pontuacaoRetiradaAdmin;
    }

    /**
    * @param  $pontos
    * @throws PropelException
    */
    public function percentualPontosVME($pontos) {

        $percentualVME = PremiosAcumuladosQuery::create()
            ->filterByPontosResgate($pontos)
            ->findOne();

        return $percentualVME->getPercentualVme();
    }
    
    /**
    * @param  $clienteId
    * @throws PropelException
    */
    public function getMaxGraduacao($clienteId) {
        $cliente = ClienteQuery::create()
        ->filterById($clienteId)
        ->findOne();
        
        $gerenciador = new GerenciadorPlanoCarreira(Propel::getConnection(), $cliente);
        $maiorGraduacaoDesc = $gerenciador->getMaiorQualificacaoAnteriorHistoricoDescricao();

        $maxGraduacaoNivel = PlanoCarreiraQuery::create()
            ->filterByGraduacao($maiorGraduacaoDesc, Criteria::EQUAL)
            ->findOne();

        return $maxGraduacaoNivel->getId();
    }
}
