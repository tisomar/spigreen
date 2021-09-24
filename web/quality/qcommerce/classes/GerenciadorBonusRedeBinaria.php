<?php

use Monolog\Logger;

/**
 * Description of GerenciadorBonusRedeBinaria
 *
 * @author André Garlini
 */
class GerenciadorBonusRedeBinaria extends GerenciadorPontos
{
    const LADO_ESQUERDO = 'ESQUERDO';
    const LADO_DIREITO  = 'DIREITO';

    /**
     * GerenciadorBonusRedeBinaria constructor.
     * @param PropelPDO $con
     * @param Logger $logger
     */
    public function __construct(\PropelPDO $con, Logger $logger)
    {
        parent::__construct($con, $logger);
    }
    
    /**
     *
     * @param Distribuicao $distribuicao
     * @throws Exception
     */
    public function geraPreview(Distribuicao $distribuicao)
    {
        if ($distribuicao->getStatus() != Distribuicao::STATUS_AGUARDANDO_PREVIEW) {
            throw new LogicException('Preview desta distribuição já foi gerado.');
        }
        
        $distribuicao->setStatus(Distribuicao::STATUS_PROCESSANDO_PREVIEW);
        $distribuicao->save($this->con);
        
        $this->con->beginTransaction();
        try {
            $clientes = ClienteQuery::create()->orderById()->find();
            foreach ($clientes as $cliente) {
                $this->distribuiBonusAoCliente($distribuicao, $cliente);
            }
            
            $distribuicao->setStatus(Distribuicao::STATUS_PREVIEW);
            $distribuicao->save($this->con);
            
            $this->con->commit();
        } catch (Exception $ex) {
            if ($this->con->isInTransaction()) {
                $this->con->rollBack();
            }
            throw $ex;
        }
    }
    
    /**
     *
     * @param Distribuicao $distribuicao
     * @throws Exception
     */
    public function confirmaDistribuicao(Distribuicao $distribuicao)
    {
        if ($distribuicao->getStatus() != Distribuicao::STATUS_AGUARDANDO) {
            if (in_array($distribuicao->getStatus(), array(Distribuicao::STATUS_AGUARDANDO_PREVIEW, Distribuicao::STATUS_PROCESSANDO_PREVIEW, Distribuicao::STATUS_PREVIEW))) {
                throw new LogicException('Preview desta distribuição ainda não já foi gerado.');
            } else {
                throw new LogicException('Esta distribuição já foi gerada.');
            }
        }
        
        $distribuicao->setStatus(Distribuicao::STATUS_PROCESSANDO);
        $distribuicao->save($this->con);
        
        $this->con->beginTransaction();
        try {
            //busca os registros de distribuicao_cliente gerados durante o preview e gera o extrato para cada cliente.
            $query = DistribuicaoClienteQuery::create()
                                    ->filterByDistribuicao($distribuicao)
                                    ->orderById();
            
            foreach ($query->find($this->con) as $distribuicaoCliente) {
                $extrato = $this->geraExtratoCliente($distribuicaoCliente);
                if ($extrato) {
                    $distribuicao->setTotalPontos($distribuicao->getTotalPontos() + $extrato->getPontos());
                }
            }
            
            $distribuicao->setStatus(Distribuicao::STATUS_DISTRIBUIDO);
            $distribuicao->save($this->con);
            
            $this->con->commit();
        } catch (Exception $ex) {
            if ($this->con->isInTransaction()) {
                $this->con->rollBack();
            }
            throw $ex;
        }
    }
    
    /**
     *
     * @param Distribuicao $distribuicao Distribuicao que deve ser executada.

     * @throws Exception
     */
    protected function distribuiBonusAoCliente(Distribuicao $distribuicao, Cliente $cliente)
    {
        $con = $this->con;
                
        $plano = $cliente->getPlano();
        if (!$plano || !$plano->getRedeBinaria()) {
            return; /* plano do cliente não concede este bonus */
        }
        
        $data = new Datetime();
        
        //verifica se ja distribuimos os pontos a este cliente
        $query = DistribuicaoClienteQuery::create()
                                ->filterByDistribuicao($distribuicao)
                                ->filterByCliente($cliente);
        if ($query->count($con) > 0) {
            $con->commit();
            return; //ja foi distribuido.
        }

        //busca o total de pontos a distribuir dos dois lados
        $pontosEsquerda = $this->getTotalPontosParaDistribuirCliente($cliente, self::LADO_ESQUERDO, true);
        $pontosDireita = $this->getTotalPontosParaDistribuirCliente($cliente, self::LADO_DIREITO, true);
        
        //verifica quantos pontos ja foram distribuidos em execucoes anteriores
        $distribuidosAnteriormente = $this->getTotalPontosProcessadosAnteriormente($cliente);

        //desconta o que foi distribuido em ciclos anteriores
        $pontosEsquerdaAtual = $pontosEsquerda - $distribuidosAnteriormente;
        $pontosDireitaAtual = $pontosDireita - $distribuidosAnteriormente;

        //escolhe o lado com menos pontos
        $pontosProcessados = min($pontosEsquerdaAtual, $pontosDireitaAtual);
        $pontosProcessados = ($pontosProcessados < 0) ? 0 : $pontosProcessados;

        $pontosTeto = $this->getTetoPontosByProcessados($pontosProcessados, $plano->getId());
        
        $pontosDistribuir = ($pontosTeto * $plano->getRedeBinaria()) / 100;

        //cria um registro de distribuicao_cliente para indicar que a distribuição deste cliente ja foi feita,
        //e para salvar a quantidade de pontos que foram considerados nessa distribuicao (é necessario nos proximos ciclos).
        $distribuicaoCliente = new DistribuicaoCliente();
        $distribuicaoCliente->setDistribuicao($distribuicao);
        $distribuicaoCliente->setCliente($cliente);
        $distribuicaoCliente->setData($data);

        //salva pontos teto usados para calculo do distribuido.
        $distribuicaoCliente->setTotalPontosUsados($pontosTeto);

        //temos que salvar os pontos que foram considerados ($pontosProcessados) isso será usado na proxima distribuicao
        $distribuicaoCliente->setTotalPontosProcessados($pontosProcessados);
        
        //salva o total a distribuir para que possamos criar o extrato quando o cliente confirmar a distribuicao
        $distribuicaoCliente->setTotalPontos($pontosDistribuir);

        $distribuicaoCliente->save($con);


        $distribuicao->save($con);
    }
    
    /**
     * Retorna os totais de pontos que deverão ser distribuidos ao cliente passado como argumento na proxima distribuição (é uma forma de preview).
     *
     * @param Cliente $cliente
     * @return array array('esquerda' => pontos pendentes na esquerda, 'direita' => pontos pendentes na direita, 'total' => esquerda + direita)
     */
    public function getTotaisProximaDistribuicaoCliente(Cliente $cliente)
    {

        $pontosEsquerda = $this->getTotalPontosParaDistribuirCliente($cliente, self::LADO_ESQUERDO);
        $pontosDireita = $this->getTotalPontosParaDistribuirCliente($cliente, self::LADO_DIREITO);
        
        $distribuidosAnteriormente = $this->getTotalPontosProcessadosAnteriormente($cliente);
        
        $pontosEsquerdaAtual = $pontosEsquerda - $distribuidosAnteriormente;
        $pontosDireitaAtual = $pontosDireita - $distribuidosAnteriormente;
                
        return array(
            'esquerda'  => $pontosEsquerdaAtual,
            'direita'   => $pontosDireitaAtual,
            'total'     => $pontosEsquerdaAtual + $pontosDireitaAtual
        );
    }
    
    /**
     *
     * @param DistribuicaoCliente $distribuicaoCliente
     * @return \Extrato|null
     */
    protected function geraExtratoCliente(DistribuicaoCliente $distribuicaoCliente)
    {
        if ($distribuicaoCliente->getTotalPontos() > 0) {
            $data = new DateTime();
            
            //cria o extrato do cliente
            $extrato = new Extrato();
            $extrato->setClienteId($distribuicaoCliente->getClienteId());
            $extrato->setTipo(Extrato::TIPO_REDE_BINARIA);
            $extrato->setPontos($distribuicaoCliente->getTotalPontos());
            $extrato->setOperacao('+');
            $extrato->setData($data);
            $extrato->setObservacao(sprintf("Distribuição rede binária %s.", $data->format('d/m/Y')));
            $extrato->setDistribuicaoId($distribuicaoCliente->getDistribuicaoId());
            $extrato->save($this->con);
            
            return $extrato;
        }
        
        return null;
    }

    /**
     * Retorna o total de pontos que devem ser distribuidos ao cliente, segundo as regras do bonus de rede binaria.
     * @warning: é retornado o total de pontos de todos os tempos. O controle de quantos pontos ja foram distribuidos em
     * execuções anteriores e quantos faltam distribuir deve ser feita pelo chamador.
     *
     * @param Cliente $cliente
     * @param $lado
     * @return float
     */
    protected function getTotalPontosParaDistribuirCliente(Cliente $cliente, $lado, $distribuirBinario = false)
    {
        $children = $cliente->getChildren(null, $this->con);
        if (count($children) == 0) {
            return 0.0;
        }
        
        $filho = null; //qual filho vamos usar para totalizar os pontos
        switch ($lado) {
            case self::LADO_ESQUERDO:
                $filho = $children[0];
                break;
            case self::LADO_DIREITO:
                $filho = (isset($children[1])) ? $children[1] : null;
                break;
            default:
                throw new InvalidArgumentException('Lado invalido.');
        }
        if (!$filho) {
            return 0.0;
        }

        // Faz o controle de quantos niveis abaixo serão contabilizados os pontos.
        $nivelCliente = $cliente->getTreeLevel();

        if (_parametro('distribuicao_binaria_nivel') > 0) {
            $nivelDistribuicao = $nivelCliente + _parametro('distribuicao_binaria_nivel');
        } else {
            $nivelDistribuicao = null;
        }


        $query = PedidoQuery::create()
                        ->addAsColumn('total', sprintf("SUM(%s)", PedidoPeer::PONTOS_REDE_BINARIA))
                        ->useClienteQuery(null, Criteria::INNER_JOIN)
                            //o proprio filho e seus descendentes de filho
                            ->filterByTreeLeft($filho->getTreeLeft(), Criteria::GREATER_EQUAL)
                            ->filterByTreeLeft($filho->getTreeRight(), Criteria::LESS_EQUAL)

                            //->filterByTreeLevel($nivelDistribuicao, Criteria::LESS_EQUAL)
                        ->endUse()
                        ->filterByCreatedAt('2018-06-01 00:00:00', Criteria::GREATER_EQUAL)
                        ->filterByStatus(PedidoPeer::STATUS_CANCELADO, Criteria::NOT_EQUAL);

        if (!is_null($nivelDistribuicao)) {
            $query
                ->useClienteQuery(null, Criteria::INNER_JOIN)
                    ->filterByTreeLevel($nivelDistribuicao, Criteria::LESS_EQUAL)
                ->endUse();
        }

        if ($distribuirBinario) {
            $data = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m') . '-01 00:00:00');
            $data->sub(new DateInterval('P1D'));
            $query->filterByCreatedAt($data->format('Y-m-d') . ' 23:59:59', Criteria::LESS_EQUAL);
        }

        if ($row = BasePeer::doSelect($query)->fetch()) {
            return (float)$row['total'];
        }
         
        return 0.0;
    }
    
    
    
    /**
     *
     * @param Cliente $cliente
     * @return float
     */
    protected function getTotalPontosProcessadosAnteriormente(Cliente $cliente)
    {
        $query = DistribuicaoClienteQuery::create()
                            ->useDistribuicaoQuery()
                                ->filterByStatus('DISTRIBUIDO')
                            ->endUse()
                            ->addAsColumn('total', sprintf('SUM(%s)', DistribuicaoClientePeer::TOTAL_PONTOS_PROCESSADOS))
                            ->filterByCliente($cliente)
                            ->filterByData('2018-06-01 00:00:00', Criteria::GREATER_EQUAL);

                
        if ($row = BasePeer::doSelect($query)->fetch()) {
            return (float)$row['total'];
        }
        
        return 0.0;
    }

    /**
     *
     * @param $pontos
     * @param $planoId
     * @return float
     */
    protected function getTetoPontosByProcessados($pontos, $planoId = null)
    {
        $query = FaixasDistribuicaoBinariaQuery::create()
            ->withColumn('PONTOS_TETO', 'teto')
            ->filterByPontuacaoInicial($pontos, Criteria::LESS_EQUAL)
            ->filterByPontuacaoFinal($pontos, Criteria::GREATER_EQUAL);

        if ($this->getPlanoValid($planoId)) {
            $query->filterByPlanoId($planoId);
        }

        if ($row = BasePeer::doSelect($query)->fetch()) {
            if (isset($row['teto']) && $row['teto'] > 0) {
                $pontos = (float)$row['teto'];
            }
        }

        return $pontos;
    }

    /**
     *
     * @param $planoId
     * @return boolean
     */
    protected function getPlanoValid($planoId)
    {
        $query = FaixasDistribuicaoBinariaQuery::create()
            ->withColumn('COUNT(PLANO_ID)', 'plano_count')
            ->filterByPlanoId($planoId);


        if ($row = BasePeer::doSelect($query)->fetch()) {
            if (isset($row['plano_count']) && $row['plano_count'] > 0) {
                return true;
            }
        }

        return false;
    }
}
