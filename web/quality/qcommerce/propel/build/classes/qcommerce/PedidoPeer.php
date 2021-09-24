<?php



/**
 * Skeleton subclass for performing query and update operations on the 'QP1_PEDIDO' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class PedidoPeer extends BasePedidoPeer
{

    CONST STATUS_ANDAMENTO = 'ANDAMENTO';
    CONST STATUS_FINALIZADO = 'FINALIZADO';
    CONST STATUS_CANCELADO = 'CANCELADO';

    CONST CLASS_KEY_PEDIDO = 1;
    CONST CLASS_KEY_CARRINHO = 2;

    CONST CONFIGURACAO_HORAS_CARRINHO_ABANDONADO = 48;

    public static function getStatusList() {
        return array(
            self::STATUS_ANDAMENTO => 'Em Andamento',
            self::STATUS_FINALIZADO => 'Finalizado',
            self::STATUS_CANCELADO => 'Cancelado',
        );
    }

    public static function getPedidoStatusList() {
        $c = PedidoStatusQuery::create()->select(array('Id', 'LabelPreConfirmacao'))->orderById()->find()->toArray();
        return array_column($c, 'LabelPreConfirmacao', 'Id');
    }

    public static function formatterPedidoIntegrationBling(Pedido $objPedido, $container) {
        /** @var Endereco $enderecoEntrega */
        $enderecoEntrega = $objPedido->getEndereco();

        $tipoCorreios = $tipoFrete = '';

        if($container->getFreteManager()->getModalidade($objPedido->getFrete())->getTitulo() == 'Sedex' ||
            $container->getFreteManager()->getModalidade($objPedido->getFrete())->getTitulo() == 'Pac'){
            $tipoCorreios = $container->getFreteManager()->getModalidade($objPedido->getFrete())->getTitulo();
        } elseif (!is_null($objPedido->getFrete())){
            $tipoFrete = $objPedido->getFrete() == 'retirada_loja' ?  'Retirada em Loja' : 'Frete grátis';
        }

        $cliente = ClientePeer::formatterClienteIntegrationBling($objPedido->getCliente(), $enderecoEntrega);
        $dadosEtiqueta = array(
            'nome' => resumo($enderecoEntrega->getNomeDestinatario().' - '.$enderecoEntrega->getIdentificacao(),'120', ''),
            'endereco' => resumo($enderecoEntrega->getLogradouro(),'50', ''),
            'numero' => resumo($enderecoEntrega->getNumero(),'10', ''),
            'complemento' => resumo($enderecoEntrega->getComplemento(),'100', ''),
            'municipio' => resumo($enderecoEntrega->getCidade()->getNome(),'30', ''),
            'uf' => $enderecoEntrega->getCidade()->getEstado()->getSigla(),
            'cep' => $enderecoEntrega->getCepBling(),
            'bairro' => resumo($enderecoEntrega->getBairro(),'30', ''),
        );

        $itens = array();

        $formaPagamentos = $objPedido->getPedidoFormaPagamentoLista();
        $pagamentoBunos = 0;

        foreach($formaPagamentos as $formaPagamento) :
            $formPag = $formaPagamento->getFormaPagamento();
            if($formPag === PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS || $formPag === PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BONUS_FRETE) :
                $pagamentoBunos = count($itensPedido) / $formaPagamento->getValorPagamento();
            endif;
        endforeach;

        $itensPedido = $objPedido->getPedidoItems();

        $arrItensPedido = array();

        foreach ($itensPedido as $objItemPedido) :
            $produto = $objItemPedido->getProdutoVariacao()->getProduto();

            $nomeIntegracao = $produto->getNomeIntegracao();
            $nome = !empty($nomeIntegracao) && !is_null($nomeIntegracao) ? $nomeIntegracao : $produto->getNome();
          
            if (!empty($produto->getPlanoId()) && $produto->isProdutoSimples()) :
                foreach ($objPedido->getPedidoItemsAll($produto->getPlanoId()) as $objItemPedido) :
                    $valorUnitario = $objItemPedido->getValorUnitario();

                    $grupoFatorCorrecao = $objItemPedido->getProdutoVariacao()->getFatorCorrecaoGrupo();
                  
                    if($grupoFatorCorrecao === 'null') :
                        echo "<br> O produto {$objItemPedido->getProdutoVariacao()->getProduto()->getNome()} não possue grupo de fator correção";die;
                    endif;
                    
                    $fatorCorrecao = PedidoPeer::getFatorCorrecao($dadosEtiqueta['uf'], $grupoFatorCorrecao);
                    $vlr_unit = number_format(($valorUnitario - $pagamentoBunos)/ $fatorCorrecao, '2', '.', '');

                    $produto = $objItemPedido->getProdutoVariacao()->getProduto();
                    
                    $nomeIntegracao = $produto->getNomeIntegracao();
                    $nome = !empty($nomeIntegracao) && !is_null($nomeIntegracao) ? $nomeIntegracao : $produto->getNome();
                    
                    $arrItensPedido[]['item'] = [
                        'codigo' => $produto->getId(),
                        'descricao' => resumo($nome, '120', ''),
                        'un' => 'un',
                        'qtde' => $objItemPedido->getQuantidade(),
                        'vlr_unit' => $vlr_unit,
                    ];
                endforeach;

                continue;
            endif;

            if (!$produto->isProdutoSimples()) :
                $arrProdutoCompostos = ProdutoCompostoQuery::create()->findByProdutoId($produto->getId());

                // $prices = [
                //     145 => [
                //         18 => 119.9,
                //         125 => 119.9
                //     ],
                //     146 => [
                //         9 => 97.93,
                //         13 => 55.93,
                //         18 => 104.93,
                //         215 => 16.80,
                //         182 => 8.31,
                //     ],
                //     147 => [
                //         8 => 103.94,
                //         9 => 90.94,
                //         12 => 110.44,
                //         13 => 51.94,
                //         18 => 97.44,
                //         125 => 129.35,
                //         129 => 91,
                //         215 => 15.60,
                //         182 => 7.65,
                //     ],
                // ];

                foreach ($arrProdutoCompostos as $objProdutoComposto):
                    $produto = $objProdutoComposto->getProdutoRelatedByProdutoCompostoId();
                    $variacao = $objProdutoComposto->getProdutoVariacao();

                    if(!$objProdutoComposto->getProdutoVariacao()) {
                        $grupoFatorCorrecao = '1';
                        $valorIntegracaoAdmin = 0;
                        $nomeIntegracao = null;
                        $nome = null;
                        $id = null;
                    }else{
                        $grupoFatorCorrecao = $objProdutoComposto->getProdutoVariacao()->getFatorCorrecaoGrupo();
                        $valorIntegracaoAdmin = $variacao->getValorIntegracaoAdmin();
                        $nomeIntegracao = $produto->getNomeIntegracao();
                        $nome = !empty($nomeIntegracao) && !is_null($nomeIntegracao) ? $nomeIntegracao : $produto->getNome();
                        $id = $produto->getId();
                    };

                    $fatorCorrecao = PedidoPeer::getFatorCorrecao($dadosEtiqueta['uf'], $grupoFatorCorrecao);

                    // $valorUnitario = $objProdutoComposto->getValorIntegracao() ?? $prices[$objProdutoComposto->getProdutoId()][$variacao->getId()] ?? $variacao->getValorFidelidade()[0];
                    $valorUnitario = $objProdutoComposto->getValorIntegracao() ?? $valorIntegracaoAdmin ?? $variacao->getValorFidelidade()[0];
                    $vlr_unit = number_format(($valorUnitario - $pagamentoBunos)/ $fatorCorrecao, '2', '.', '');

                    $arrItensPedido[]['item'] = [
                        'codigo' => $id,
                        'descricao' => resumo($nome, '120', ''),
                        'un' => 'un',
                        'qtde' => $objProdutoComposto->getEstoqueQuantidade(),
                        'vlr_unit' => $vlr_unit,
                    ];
                endforeach;
            else:
                
                $grupoFatorCorrecao = $objItemPedido->getProdutoVariacao()->getFatorCorrecaoGrupo();
                $fatorCorrecao = PedidoPeer::getFatorCorrecao($dadosEtiqueta['uf'], $grupoFatorCorrecao);

                $valorUnitario = $objItemPedido->getValorUnitario();
                $vlr_unit = number_format(($valorUnitario - $pagamentoBunos)/ $fatorCorrecao, '2', '.', '');

                $arrItensPedido[]['item'] = [
                    'codigo' => $produto->getId(),
                    'descricao' => resumo($nome, '120', ''),
                    'un' => 'un',
                    'qtde' => $objItemPedido->getQuantidade(),
                    'vlr_unit' => $vlr_unit,
                ];
            endif;
        endforeach;

        return [
            'data' => $objPedido->getDataConfirmacaoPagamento('d/m/Y'),
            'numero' => resumo($objPedido->getId(), '10', ''),
            'numero_loja' => resumo($objPedido->getId(), '50', ''),
            'cliente' => $cliente,
            'transporte' => array(
                'transportadora' => $tipoFrete,
                'servico_correios' => $tipoCorreios,
                'dados_etiqueta' => $dadosEtiqueta,
            ),
            'itens' => $arrItensPedido,
            'vlr_frete' => number_format($objPedido->getValorEntrega(),'2','.',''),
            'vlr_desconto' => number_format($objPedido->getValorDesconto(),'2','.',''),
        ];
    }

    public static function formatterPedidoServiceIntegrationBling(Pedido $objPedido, $container) {


        /** @var Endereco $enderecoEntrega */
        $enderecoEntrega = $objPedido->getEndereco();

        $tipoCorreios = $tipoFrete = '';

        if($container->getFreteManager()->getModalidade($objPedido->getFrete())->getTitulo() == 'Sedex' ||
            $container->getFreteManager()->getModalidade($objPedido->getFrete())->getTitulo() == 'Pac'){
            $tipoCorreios = $container->getFreteManager()->getModalidade($objPedido->getFrete())->getTitulo();
        } elseif (!is_null($objPedido->getFrete())){
            $tipoFrete = $objPedido->getFrete() == 'retirada_loja' ?  'Retirada em Loja' : '';
        }

        $cliente = ClientePeer::formatterClienteIntegrationBling($objPedido->getCliente(), $enderecoEntrega);
        $dadosEtiqueta = array(
            'nome' => resumo($enderecoEntrega->getNomeDestinatario().' - '.$enderecoEntrega->getIdentificacao(),'120', ''),
            'endereco' => resumo($enderecoEntrega->getLogradouro(),'50', ''),
            'numero' => resumo($enderecoEntrega->getNumero(),'10', ''),
            'complemento' => resumo($enderecoEntrega->getComplemento(),'100', ''),
            'municipio' => resumo($enderecoEntrega->getCidade()->getNome(),'30', ''),
            'uf' => $enderecoEntrega->getCidade()->getEstado()->getSigla(),
            'cep' => $enderecoEntrega->getCepBling(),
            'bairro' => resumo($enderecoEntrega->getBairro(),'30', ''),
        );

        $itens = array();

        $itensPedido = $objPedido->getPedidoItems();

        $arrItensPedido = array();

        foreach($itensPedido as $objItemPedido){
            $produto = $objItemPedido->getProdutoVariacao()->getProduto();

            $nomeIntegracao = $produto->getNomeIntegracao();
            $nome = !empty($nomeIntegracao) && !is_null($nomeIntegracao) ? $nomeIntegracao : $produto->getNome();

            if (!empty($produto->getPlanoId())):
                if (!$produto->isProdutoSimples()):
                    $arrProdutoCompostos = ProdutoCompostoQuery::create()->findByProdutoId($produto->getId());

                    $prices = [
                        145 => [
                            18 => 119.9
                        ],
                        146 => [
                            9 => 97.93,
                            13 => 55.93,
                            18 => 104.93,
                            154 => 16.80,
                            182 => 8.31,
                        ],
                        147 => [
                            8 => 103.94,
                            9 => 90.94,
                            12 => 110.44,
                            13 => 51.94,
                            18 => 97.44,
                            125 => 129.35,
                            129 => 91,
                            154 => 15.60,
                            182 => 7.65,
                        ],
                    ];

                    foreach ($arrProdutoCompostos as $objProdutoComposto):
                        $produto = $objProdutoComposto->getProdutoRelatedByProdutoCompostoId();
                        $variacao = $objProdutoComposto->getProdutoVariacao();
                        $nomeIntegracao = $produto->getNomeIntegracao();
                        $nome = !empty($nomeIntegracao) && !is_null($nomeIntegracao) ? $nomeIntegracao : $produto->getNome();

                        $arrItensPedido[]['item'] = [
                            'codigo' => $produto->getId() . 'S',
                            'descricao' => resumo($nome . 'S', '120', ''),
                            'un' => 'un',
                            'qtde' => $objProdutoComposto->getEstoqueQuantidade(),
                            'vlr_unit' => number_format($prices[$objProdutoComposto->getProdutoId()][$variacao->getId()] ?? $variacao->getValorFidelidade()[0], 2, '.', ''),
                        ];
                    endforeach;
                else:
                    foreach ($objPedido->getPedidoItemsAll($produto->getPlanoId()) as $objItemPedido):
                        $produto = $objItemPedido->getProdutoVariacao()->getProduto();

                        $nomeIntegracao = $produto->getNomeIntegracao();
                        $nome = !empty($nomeIntegracao) && !is_null($nomeIntegracao) ? $nomeIntegracao : $produto->getNome();

                        $arrItensPedido[]['item'] = [
                            'codigo' => $produto->getId() . 'S',
                            'descricao' => resumo($nome . 'S', '120', ''),
                            'un' => 'un',
                            'qtde' => $objItemPedido->getQuantidade(),
                            'vlr_unit' => number_format($objItemPedido->getValorUnitario(), 2, '.', ''),
                        ];
                    endforeach;
                endif;
            else:
                $arrItensPedido[]['item'] = [
                    'codigo' => $produto->getId().'S',
                    'descricao' => resumo($nome.'S', '120', ''),
                    'un' => 'un',
                    'qtde' => $objItemPedido->getQuantidade(),
                    'vlr_unit' => number_format($objItemPedido->getValorUnitario(), 2, '.', ''),
                ];
            endif;
        }


        return array(

            'data' => $objPedido->getDataConfirmacaoPagamento('d/m/Y'),
            'numero' => resumo($objPedido->getId().'S', '10', ''),
            'numero_loja' => resumo($objPedido->getId().'S', '50', ''),
            'cliente' => $cliente,
            'transporte' => array(
                'transportadora' => $tipoFrete,
                'servico_correios' => $tipoCorreios,
                'dados_etiqueta' => $dadosEtiqueta,
            ),
            'itens' => $arrItensPedido,
            'vlr_frete' => number_format(0.00,'2','.',''),
            'vlr_desconto' => number_format(0.00,'2','.',''),

        );

    }

    public static function getValorTotalPedidosUltimoMes($clienteId)
    {
        $start = new DateTime('first day of last month');
        $start->setTime(0, 0, 0);

        $end = new DateTime('first day of this month');
        $end->setTime(0, 0, -1);

        $pedido = PedidoQuery::create()
            ->usePedidoStatusHistoricoQuery()
                ->filterByPedidoStatusId(1)
                ->filterByIsConcluido(1)
            ->endUse()
            ->select(['valorTotalItens'])
            ->WithColumn('IFNULL(SUM(VALOR_ITENS), 0)', 'valorTotalItens')
            ->filterByClienteId($clienteId)
            ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
            ->filterByCreatedAt($start, Criteria::GREATER_EQUAL)
            ->filterByCreatedAt($end, Criteria::LESS_EQUAL)
            ->findOne();

        return (float) $pedido;
    }

    public static function getValorTotalPedidosMesAtual($clienteId)
    {
        $start = new DateTime('first day of this month');
        $start->setTime(0, 0, 0);

        $end = new DateTime('first day of next month');
        $end->setTime(0, 0, -1);

        $pedido = PedidoQuery::create()
            ->usePedidoStatusHistoricoQuery()
            ->filterByPedidoStatusId(1)
            ->filterByIsConcluido(1)
            ->endUse()
            ->select(['valorTotalItens'])
            ->WithColumn('IFNULL(SUM(VALOR_ITENS), 0)', 'valorTotalItens')
            ->filterByClienteId($clienteId)
            ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
            ->filterByCreatedAt($start, Criteria::GREATER_EQUAL)
            ->filterByCreatedAt($end, Criteria::LESS_EQUAL)
            ->findOne();

        return (float) $pedido;
    }

    public static function getPontosPedidosPeriodo($clienteId, $inicio, $fim)
    {
        $pontosLojaClienteFinal = PedidoQuery::create()
            ->select(['valorTotalPontos'])
            ->withColumn(sprintf('IFNULL(SUM(%s * %s), 0)', PedidoItemPeer::VALOR_PONTOS_UNITARIO, PedidoItemPeer::QUANTIDADE), 'valorTotalPontos')
            ->filterByHotsiteClienteId($clienteId)
            ->usePedidoItemQuery()
                ->useProdutoVariacaoQuery()
                    ->useProdutoQuery()
                        ->filterByPlanoId(null, Criteria::ISNULL)
                    ->endUse()
                ->endUse()
            ->endUse()
            ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
            ->filterByDataPagamentoPeriodo($inicio, $fim)
            ->findOne();

        /*$planosClientesPreferencial = PlanoQuery::create()
            ->select([PlanoPeer::ID])
            ->filterByPlanoClientePreferencial(true)
            ->find()
            ->toArray();*/

        $pontosPreferencialRecompra = PedidoQuery::create()
            ->select(['valorTotalPontos'])
            ->withColumn(sprintf('IFNULL(SUM(%s * %s), 0)', PedidoItemPeer::VALOR_PONTOS_UNITARIO, PedidoItemPeer::QUANTIDADE), 'valorTotalPontos')
            ->useExtratoClientePreferencialQuery()
                ->filterByOperacao('+')
                ->useClienteQuery('c1')
                    ->filterByClienteIndicadorId($clienteId)
                ->endUse()
            ->endUse()
            ->useClienteQuery('c2')
                ->filterByClienteIndicadorId($clienteId)
            ->endUse()
            ->usePedidoItemQuery()
                ->useProdutoVariacaoQuery()
                    ->useProdutoQuery()
                        ->filterByPlanoId(null, Criteria::ISNULL)
                    ->endUse()
                ->endUse()
            ->endUse()
            ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
            ->filterByDataPagamentoPeriodo($inicio, $fim)
            ->findOne();

        /*$pontosPreferencialAdesao = PedidoQuery::create()
                ->select(['valorTotalPontos'])
                ->withColumn(sprintf('IFNULL(SUM(%s * %s), 0)', PedidoItemPeer::VALOR_PONTOS_UNITARIO, PedidoItemPeer::QUANTIDADE), 'valorTotalPontos')
                ->useExtratoClientePreferencialQuery()
                    ->filterByOperacao('+')
                    ->useClienteQuery('c1')
                        ->filterByClienteIndicadorId($clienteId)
                    ->endUse()
                ->endUse()
                ->useClienteQuery('c2')
                    ->filterByClienteIndicadorId($clienteId)
                ->endUse()
                ->usePedidoItemQuery()
                    ->useProdutoVariacaoQuery()
                        ->useProdutoQuery()
                            ->filterByPlanoId($planosClientesPreferencial)
                        ->endUse()
                    ->endUse()
                ->endUse()
                ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
                ->filterByDataPagamentoPeriodo($inicio, $fim)
                ->findOne();*/

        $pontosPessoais = PedidoQuery::create()
            ->select(['valorTotalPontos'])
            ->withColumn('IFNULL(SUM(VALOR_PONTOS), 0)', 'valorTotalPontos')
            ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
            ->filterByClienteId($clienteId)
            ->filterByDataPagamentoPeriodo($inicio, $fim)
            ->findOne();

        $total = (int) $pontosLojaClienteFinal;
        $total += (int) $pontosPreferencialRecompra;
        // $total += (int) $pontosPreferencialAdesao;
        $total += (int) $pontosPessoais;

        return $total;
    }

    public static function getValorTotalPontosPedidosUltimoMes($clienteId)
    {
        $start = new DateTime('first day of last month');
        $start->setTime(0, 0, 0);

        $end = new DateTime('first day of this month');
        $end->setTime(0, 0, -1);

        return self::getPontosPedidosPeriodo($clienteId, $start, $end);
    }

    public static function getValorTotalPontosPedidosMesAtual($clienteId)
    {
        $start = new DateTime('first day of this month');
        $start->setTime(0, 0, 0);

        $end = new DateTime('first day of next month');
        $end->setTime(0, 0, -1);

        return self::getPontosPedidosPeriodo($clienteId, $start, $end);
    }

    public function getTotalPedidosPeriodo($clienteId, $inicio, $fim)
    {
        $total = PedidoQuery::create()
            ->usePedidoStatusHistoricoQuery()
                ->filterByPedidoStatusId(1)
                ->filterByIsConcluido(1)
            ->endUse()
            ->select(['valorTotalPontos'])
            ->withColumn('COUNT(Pedido.Id)', 'valorTotalPontos')
            ->where(
                sprintf(
                    'IFNULL(%s, %s) = ?',
                    PedidoPeer::HOTSITE_CLIENTE_ID,
                    PedidoPeer::CLIENTE_ID
                ),
                $clienteId,
                \PDO::PARAM_INT
            )
            ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
            ->filterByCreatedAt(['min' => $inicio, 'max' => $fim])
            ->findOne();

        return (int) $total;
    }

    public function getTotalPedidoMesAtual($clienteId)
    {
        $start = new DateTime('first day of this month');
        $start->setTime(0, 0, 0);

        $end = new DateTime('first day of next month');
        $end->setTime(0, 0, -1);

        return self::getTotalPedidosPeriodo($clienteId, $start, $end);
    }

    /**
     * @param $clienteId
     * @param $inicio
     * @param $fim
     * @return Pedido|null
     */
    public function getPedidosPeriodo($clienteId, $inicio, $fim)
    {
        $pedidos = PedidoQuery::create()
            ->usePedidoStatusHistoricoQuery()
                ->filterByPedidoStatusId(1)
                ->filterByIsConcluido(1)
            ->endUse()
            ->where(
                sprintf(
                    'IFNULL(%s, %s) = ?',
                    PedidoPeer::HOTSITE_CLIENTE_ID,
                    PedidoPeer::CLIENTE_ID
                ),
                $clienteId,
                \PDO::PARAM_INT
            )
            ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
            ->filterByCreatedAt(['min' => $inicio, 'max' => $fim])
            ->find();

        return $pedidos;
    }

    /**
     * @param $clienteId
     * @return Pedido|null
     * @throws Exception
     */
    public function getPedidosMesAtual($clienteId)
    {
        $start = new DateTime('first day of this month');
        $start->setTime(0, 0, 0);

        $end = new DateTime('first day of next month');
        $end->setTime(0, 0, -1);

        return self::getPedidosPeriodo($clienteId, $start, $end);
    }

    /**
     * @param $uf
     * @throws Exception
     */
    public function getFatorCorrecao($uf, $grupo) {

        $grupo = $grupo ?? 1;
        $uf = strtoupper($uf);

        $fator_correcao = ParametroQuery::create()
            ->filterByAlias("%fator-correcao-g$grupo.$uf%", Criteria::LIKE)
            ->useParametroGrupoQuery()
                ->filterByAlias("fator_correcao_g$grupo")
            ->endUse()
            ->findOne();

        $valor = $fator_correcao->getValor();
        $valor = trim($valor);
        $valor = str_replace(',', '.', $valor);
        return $valor ?? 1;
    }

}
