<?php

/**
 * Skeleton subclass for representing a row from the 'qp1_produto_variacao' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ProdutoVariacao extends BaseProdutoVariacao
{

    /**
     * Faz as validações de negócio que não são feitas no schema.xml
     *
     * @param $erros.
     * @param null $columns
     *
     * @return bool Retorna true caso não tenha encontrado erros.
     */
    public function myValidate(&$erros, $columns = null)
    {

        parent::myValidate($erros, $columns);

        /**
         * Não permite que o usuário insira um valor promocional
         * maior que o valor real do produto.
         */
        if ($this->getValorPromocional() && $this->getValorPromocional() >= $this->getValorBase()) {
            $erros[] = "O valor promocional deve ser menor que o valor base do produto.";
        }

        return count($erros) == 0;
    }

    public function postUpdate(PropelPDO $con = null)
    {
        $this->enviaAvisoDisponivel();
        return parent::postUpdate();
    }

    public function postSave(\PropelPDO $con = null)
    {
        $this->enviaAvisoDisponivel();

        /**
         * Se a coluna modificada for VALOR_BASE ou VALOR_PROMOCIONAL (verificação feita no preSave()),
         * precisamos atualizar os valores dos preços nas tabelas de preços.
         */
        if ($this->updateValueInTabelaPreco) {
            $colTabelaPreco = TabelaPrecoQuery::create()->find();
            if (count($colTabelaPreco)) {
                foreach ($colTabelaPreco as $objTabelaPreco) {
                    TabelaPrecoPeer::addProdutoVariacaoToTabelaPreco($objTabelaPreco, $this);
                }
            }
        }

        return parent::postSave($con);
    }

    /**
     * Identifica se o valor base ou promocional foi alterado para efetuar a alteração em massa nas tabelas de valores
     *
     * @param PropelPDO $con
     * @return bloolean|void
     */
    public $updateValueInTabelaPreco = false;
    public function preSave(PropelPDO $con = null)
    {
        foreach ($this->getModifiedColumns() as $column) {
            if (in_array($column, array(ProdutoVariacaoPeer::VALOR_BASE, ProdutoVariacaoPeer::VALOR_PROMOCIONAL))) {
                $this->updateValueInTabelaPreco = true;
                continue;
            }
        }
        return parent::preSave($con);
    }

    /**
     * Verifica se a alteração do produto o tornou disponível e marca na tabela de avisos
     * que o sistema poderá avisar os clientes que o produto estará disponível.
     */
    public function enviaAvisoDisponivel()
    {

        if ($this->isDisponivel()) {
            ProdutoInteresseQuery::create()

                ->filterByProdutoVariacaoId($this->getId())
                ->filterByEnviarAviso(false)

                ->update(array(
                    'EnviarAviso' => true
                ));

            /**
             * Quando todas as variações de um produto estiverem indisponíveis para venda,
             * o sistema armazenará o valor da variação master na tabela de interesse.
             * Ou seja, quando qualquer uma das variações estiver disponível, o sistema enviará
             * o alerta ao consumidor avisando que o produto interesse dele está disponível para venda,
             * mesmo que sejam apenas algumas variações.
             */
            if (!$this->getIsMaster()) {
                $this->getProduto()->getProdutoVariacao()->enviaAvisoDisponivel();
            }
        }
    }


    public function getAltura()
    {
        return $this->getProduto()->getAltura();
    }

    public function getLargura()
    {
        return $this->getProduto()->getLargura();
    }

    public function getComprimento()
    {
        return $this->getProduto()->getComprimento();
    }

    public function getPeso()
    {
        return $this->getProduto()->getPeso();
    }

    public function setValorBase($v)
    {
        if (!is_numeric($v)) {
            $v = str_replace(array('R$', ' '), null, $v);
            $v = format_number($v, UsuarioPeer::LINGUAGEM_INGLES);
        }

        return parent::setValorBase($v);
    }

    /**
     * @return float
     * @throws PropelException
     */
    public function getValorBase()
    {
        global $container;

        if (Config::get('cliente.has_tabela_preco')
            && strpos($container->getRequest()->getPathInfo(), '/admin/') === false
            && strpos($container->getRequest()->getPathInfo(), 'checkout/pagamento') === false
            && ClientePeer::isAuthenticad()) {
            $tabelaId = ClientePeer::getClienteLogado()->getTabelaPrecoId();
            if (!is_null($tabelaId)) {
                $valor = TabelaPrecoVariacaoQuery::create()
                    ->select(array('ValorBase'))
                    ->filterByTabelaPrecoId($tabelaId)
                    ->filterByProdutoVariacaoId($this->getId())
                    ->findOne();
            } else {
                $valor = parent::getValorBase();
            }
        } else {
            $valor = parent::getValorBase();
        }

        $clienteLogado = null;
        if (ClientePeer::isAuthenticad()) {
            $clienteLogado = ClientePeer::getClienteLogado(true);
        }

        //$franqueado = ClientePeer::getFranqueadoSelecionado($container);
        //$isNewReseller = ClientePeer::isResellerActived();
        $isNewReseller = false;
        $aplicarDesconto = false;

        if (stripos($container->getRequest()->getPathInfo(), '/admin/') === false) {
            $aplicarDesconto = true;
        }

        //Clientes que possuem um plano ativo ganham desconto configurado em "desconto_clientes_plano_ativo" (apenas se o produto não for um kit de adesão)
        if ($clienteLogado && $clienteLogado->getPlano()
            && (!$this->getProduto()->isKitAdesao())
            && (!$this->getProduto()->isMensalidade())
            && (!$this->getProduto()->getTaxaCadastro())
            && $aplicarDesconto
        ) {
/*
            $descontoFidelidade = DescontoFidelidadePeer::getDescontoFidelidadeActive($clienteLogado->getNumeroMesesAtivo());

            if($descontoFidelidade) {
                //aplica o desconto no valor base
                $valor = $valor - ($valor * ($descontoFidelidade->getPercentualDesconto() / 100));
            }
*/
        } //elseif (($franqueado && $franqueado->getPlano())
//            && !$isNewReseller
//            && (!$this->getProduto()->isKitAdesao())
//            && (!$this->getProduto()->isMensalidade())
//            && (!$this->getProduto()->getTaxaCadastro())) {
//
//            $descontoFidelidade = DescontoFidelidadePeer::getDescontoFidelidadeActive($franqueado->getNumeroMesesAtivo());
//
//            if($descontoFidelidade) {
//                //aplica o desconto no valor base
//                $valor = $valor - ($valor * ($descontoFidelidade->getPercentualDesconto() / 100));
//            }
//
//        }

        //Se o cliente estiver adquirindo um novo plano, ele paga apenas a diferença.
//        if (false && $clienteLogado && ($planoAtual = $clienteLogado->getPlano()) && $this->getProduto()->isKitAdesao() && $this->getProduto()->getPlanoId() != $planoAtual->getId()) {
//            //Busca o produto do plano atual
//            $produtoPlanoAtual = ProdutoQuery::create()->findOneByPlanoId($planoAtual->getId());
//            if ($produtoPlanoAtual) {
//                $valorPlanoAtual = $produtoPlanoAtual->getValor();
//                if ($valor > $valorPlanoAtual) {
//
//                    $valor = $valor - $valorPlanoAtual; //aplica a diferenca.
//
//                } // cliente escolheu um plano mais barato. O que fazer ????????????
//            }
//        }

        return $valor;
    }

    public function getValorPromocional()
    {
        global $container;

        if (Config::get('cliente.has_tabela_preco')
            && strpos($container->getRequest()->getPathInfo(), '/admin/') === false
            && strpos($container->getRequest()->getPathInfo(), 'checkout/pagamento') === false
            && ClientePeer::isAuthenticad()) {
            $tabelaId = ClientePeer::getClienteLogado()->getTabelaPrecoId();
            if (!is_null($tabelaId)) {
                $valor = TabelaPrecoVariacaoQuery::create()
                    ->select(array('ValorPromocional'))
                    ->filterByTabelaPrecoId($tabelaId)
                    ->filterByProdutoVariacaoId($this->getId())
                    ->findOne();
            } else {
                $valor = parent::getValorPromocional();
            }
        } else {
            $valor = parent::getValorPromocional();
        }
/*
        $clienteLogado = null;
        if (ClientePeer::isAuthenticad()) {
            $clienteLogado = ClientePeer::getClienteLogado(true);
        }

        $franqueado = ClientePeer::getFranqueadoSelecionado($container);
        $isNewReseller = ClientePeer::isResellerActived();

        $aplicarDesconto = false;

        if(stripos($container->getRequest()->getPathInfo(), '/admin/') === false){
            $aplicarDesconto = true;
        }


        //Clientes que possuem um plano ativo ganham desconto configurado em "desconto_clientes_plano_ativo" (apenas se o produto não for um kit de adesão)
        if ($clienteLogado && $clienteLogado->getPlano()
            && (!$this->getProduto()->isKitAdesao())
            && (!$this->getProduto()->isMensalidade())
            && (!$this->getProduto()->getTaxaCadastro())
            && $aplicarDesconto
        ) {

            $descontoFidelidade = DescontoFidelidadePeer::getDescontoFidelidadeActive($clienteLogado->getNumeroMesesAtivo());

            if($descontoFidelidade) {
                //aplica o desconto no valor base
                $valor = $valor - ($valor * ($descontoFidelidade->getPercentualDesconto() / 100));
            }
        } elseif (($franqueado && $franqueado->getPlano())
            && !$isNewReseller
            && (!$this->getProduto()->isKitAdesao())
            && (!$this->getProduto()->isMensalidade())
            && (!$this->getProduto()->getTaxaCadastro())) {

            $descontoFidelidade = DescontoFidelidadePeer::getDescontoFidelidadeActive($franqueado->getNumeroMesesAtivo());

            if($descontoFidelidade) {
                //aplica o desconto no valor base
                $valor = $valor - ($valor * ($descontoFidelidade->getPercentualDesconto() / 100));
            }

        }
*/
        return $valor;
    }


    public function getValorDistribuidor()
    {
        global $container;


        $valor = parent::getValorDistribuidor();


        $clienteLogado = null;
        if (ClientePeer::isAuthenticad()) {
            $clienteLogado = ClienteQuery::create()->findPk(ClientePeer::getClienteLogado()->getId());
        }

        $aplicarDesconto = false;

        if (stripos($container->getRequest()->getPathInfo(), '/admin/') === false) {
            $aplicarDesconto = true;
        }
/*
        //Clientes que possuem um plano ativo ganham desconto configurado em "desconto_clientes_plano_ativo" (apenas se o produto não for um kit de adesão)
        if ($clienteLogado && $clienteLogado->getPlano() &&
            (!$this->getProduto()->isKitAdesao())
            && (!$this->getProduto()->isMensalidade())
            && (($desconto = Config::get('desconto_clientes_plano_ativo')) > 0)
            && $aplicarDesconto
        ) {

            //aplica o desconto no valor base
            $valor = $valor - ($valor * ($desconto / 100));
        }
*/
        return $valor;
    }

    public function setValorPromocional($v)
    {
        if (!is_numeric($v)) {
            $v = str_replace(array('R$', ' '), null, $v);
            $v = format_number($v, UsuarioPeer::LINGUAGEM_INGLES);
        }
        return parent::setValorPromocional($v);
    }

    /**
     * @param float $v
     * @return ProdutoVariacao
     */

    public function setValorDistribuidor($v)
    {
        if (!is_numeric($v)) {
            $v = str_replace(array('R$', ' '), null, $v);
            $v = format_number($v, UsuarioPeer::LINGUAGEM_INGLES);
        }
        return parent::setValorDistribuidor($v); // TODO: Change the autogenerated stub
    }

    /**
     * Retorna o valor promocional caso esteja preenchido. Do contrário, retorna o valor base do produto.
     *
     * @return float
     * @throws PropelException
     */
    public function getValor()
    {
        $valor = (ClientePeer::isAuthenticad()
            && !is_null(ClientePeer::getClienteLogado(true)->getPlano())
            && $this->getValorDistribuidor() > 0)
            ? $this->getValorDistribuidor()
            : (($this->getValorPromocional() > 0) ? $this->getValorPromocional() : $this->getValorBase());
        return $valor;
    }

    public function getPontosAtivacaoPeriodo($clienteId, $start, $end) {

        $total = PedidoQuery::create()
           ->usePedidoStatusHistoricoQuery()
               ->filterByPedidoStatusId(1)
               ->filterByIsConcluido(1)
           ->endUse()
           ->select(['valorTotalPontos'])
           ->withColumn('IFNULL(SUM(VALOR_PONTOS), 0)', 'valorTotalPontos')
           ->condition('cond1', 'CLIENTE_ID = ?', $clienteId, \PDO::PARAM_INT)
           ->condition('cond2', 'HOTSITE_CLIENTE_ID = ?', $clienteId, \PDO::PARAM_INT)
           ->combine(['cond1', 'cond2'], Criteria::LOGICAL_OR, 'cond1-2')
           ->where(['cond1-2'])
           ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
           ->filterByCreatedAt(['min' => $start, 'max' => $end])
           ->findOne();
       return (float) $total;
    }

    /**
     * Retorna os valores distintos de cada cliente por quantidade de meses ativos
     *
     * @return array
     */
    public function getValorFidelidade()
    {
        if ($this->getProduto()->isKitAdesao()) :
            $produto = $this->getProduto();
            $valorProduto = $produto->getValorBase();

            $valorCobrado = $produto->getValorPromocional() > 0
                ? $produto->getValorPromocional()
                : $valorProduto;

            $valorDesconto = $valorProduto - $valorCobrado;

            return [$valorCobrado, $valorDesconto, null];
        endif;

        if ($this->getValorPromocional() > 0) :
            $valorProduto = $this->getValorBase();
            $valorCobrado = $this->getValorPromocional();

            $valorDesconto = $valorProduto - $valorCobrado;

            $porcentagem = $valorDesconto * 100 / $valorProduto;

            return [$valorCobrado, $porcentagem, null];
        endif;

        if (!$this->getProduto()->getAplicaDescontoPlano()) :
            $valorProduto = $this->getValorBase();

            return [$valorProduto, 0, null];    
        endif;

        $cliente = ClientePeer::getClienteLogado(true);
        
        if($cliente) :
            $clienteAtivoPermanente = in_array($cliente->getId(), ClientePeer::getClientesAtivosPermanente());
            
            $maximoMeses = 0;

            if (!empty($cliente->getPlanoId())) :
                $maximoMeses = PlanoDescontoFidelidadeQuery::create()
                ->select(['maximoMeses'])
                ->withColumn(
                    sprintf('IFNULL(MAX(%s), 0)', PlanoDescontoFidelidadePeer::MES_INICIAL),
                    'maximoMeses'
                    )
                    ->filterByPlanoId($cliente->getPlanoId())
                    ->findOne();
            endif;
       
            if ($clienteAtivoPermanente) :
                $mesesAtivo = $maximoMeses;
            else :
                $mesesAtivo = 0;
    
                $start = new DateTime('first day of this month');
                $start->setTime(0, 0, 0);
    
                $end = new DateTime('last day of this month');
                $end->setTime(23, 59, 59);
    
                if (ClientePeer::getClienteAtivoMensal($cliente->getId(), $start, $end)) :
                    $mesesAtivo++;
                endif;
    
                $start->modify('first day of previous month');
                $end->modify('last day of previous month');
    
                $cont = 1;
                do {
                    if (ClientePeer::getClienteAtivoMensal($cliente->getId(), $start, $end)) :
                        $mesesAtivo++;
                    else :
                        break;
                    endif;
    
                    $start->modify('first day of previous month');
                    $end->modify('last day of previous month');
                } while (++$cont <= $maximoMeses);
            endif;
       
            $percPeriodo = 0;
            $percGraduacao = 0;

            if (!empty($cliente->getPlanoId())) :
                $descontoFidelidade = PlanoDescontoFidelidadeQuery::create()
                    ->filterByMesInicial($mesesAtivo, Criteria::LESS_EQUAL)
                    ->filterByPlanoId($cliente->getPlanoId())
                    ->orderByMesInicial(Criteria::DESC)
                    ->findOne();
    
                if (!empty($descontoFidelidade)) :
                    $percPeriodo = $descontoFidelidade->getPercentual() ?? 0;
                endif;
    
                $graduacaoAtual = PlanoCarreiraHistoricoQuery::create()
                    ->filterByClienteId($cliente->getId())
                    ->filterByMes(date('m'))
                    ->filterByAno(date('Y'))
                    ->findOne();
    
                if (!empty($graduacaoAtual)) :
                    $nivelAtual = $graduacaoAtual->getPlanoCarreira()->getNivel();
    
                    $descontoGraduacao = PlanoDescontoFidelidadeGraduacaoQuery::create()
                        ->usePlanoCarreiraQuery()
                            ->filterByNivel($nivelAtual, Criteria::LESS_EQUAL)
                            ->orderByNivel(Criteria::DESC)
                        ->endUse()
                        ->filterByPlanoId($cliente->getPlanoId())
                        ->findOne();
    
                    if (!empty($descontoGraduacao)) :
                        $percGraduacao = $descontoGraduacao->getPercentual() ?? 0;
                    endif;
                endif;
            endif;
        endif;

        $porcentagem = max($percPeriodo, $percGraduacao);

        $valorProduto = $this->getValorBase();

        $valorCobrado = $valorProduto * (100 - $porcentagem) / 100;

        return [$valorCobrado, $porcentagem, $mesesAtivo];
    }

    /**
     * Retorna os valores distintos de cada cliente por quantidade de meses ativos
     *
     * @return array
     */
    public function getValorProdutoPlano(\BasePlano $plano)
    {
        if ($this->getProduto()->isKitAdesao()) :
            $produto = $this->getProduto();
            $valorProduto = $produto->getValorBase();

            $valorCobrado = $produto->getValorPromocional() > 0
                ? $produto->getValorPromocional()
                : $valorProduto;

            $valorDesconto = $valorProduto - $valorCobrado;

            return [$valorCobrado, $valorDesconto, null];
        endif;

        if ($this->getValorPromocional() > 0) :
            $valorProduto = $this->getValorBase();
            $valorCobrado = $this->getValorPromocional();

            $valorDesconto = $valorProduto - $valorCobrado;

            $porcentagem = $valorDesconto * 100 / $valorProduto;

            return [$valorCobrado, $porcentagem, null];
        endif;

        if (!$this->getProduto()->getAplicaDescontoPlano()) :
            $valorProduto = $this->getValorBase();

            return [$valorProduto, 0, null];
        endif;

        $valorProduto = $this->getValorBase();

        $criteria = PlanoDescontoFidelidadeQuery::create()->orderByMesInicial();
        $descontos = $plano->getPlanoDescontoFidelidades($criteria);

        if ($descontos->count() > 0):
            $porcentagem = $descontos->getFirst()->getPercentual();

            $valorProduto *= (100 - $porcentagem) / 100;
        endif;

        return $valorProduto;
    }

    /**
     * Retorna TRUE se o produto estiver com o valor de promoção preenchido.
     *
     * @return boolean true ou false
     */
    public function isPromocao()
    {
        if (ClientePeer::isAuthenticad()
            && !is_null(ClientePeer::getClienteLogado(true)->getPlano())
            && $this->getValorDistribuidor() > 0) {
            return true;
        }
        return ($this->getValorPromocional() > 0);
    }

    /**
     *
     * @param ProdutoAtributo $objProdutoAtributo
     * @return string
     */
    public function getDescricaoAtributo(ProdutoAtributo $objProdutoAtributo)
    {
        $objProdutoVariacaoAtributo = ProdutoVariacaoAtributoPeer::retrieveByPK($this->getId(), $objProdutoAtributo->getId());
        if ($objProdutoVariacaoAtributo instanceof ProdutoVariacaoAtributo) {
            return $objProdutoVariacaoAtributo->getDescricao();
        } else {
            return '';
        }
    }

    /**
     * Retorna a descrição de parcelas.
     * @return String
     */
    public function getDescricaoParcelado()
    {
        $valor = $this->getValor();
        $parcelas = $this->getProduto()->getNumeroMaximoParcelas($valor);

        $valorParcela = $valor / $parcelas;

        return $parcelas . 'x de R$ ' . format_number($valorParcela);
    }

    public function isDisponivel()
    {

        $disponivel = true;

        if (!$this->getDisponivel()) {
            $disponivel = false;
        }

        if ($this->getSomaTotalEstoque() < 1) {
            $disponivel = false;
        }

        return $disponivel;
    }

    /**
     * Reduz uma quantidade do estoque.
     * @param int $quantidade
     */
    public function diminuirEstoque($quantidade, $pedidoId = null)
    {
        $produto = $this->getProduto();

        if (ProdutoPeer::PRODUTO_TAXA_ID == $produto->getId()) {
            return 'taxa_cadastro';
        }

        if ($produto->getTipoProduto() == 'SIMPLES') {
            //$this->setEstoqueAtual($this->getEstoqueAtual() - $quantidade);
            //$this->save();

            $objEstoque = new EstoqueProduto();
            $objEstoque->setProdutoId($this->getProdutoId());
            $objEstoque->setProdutoVariacaoId($this->getId());
            if (!is_null($pedidoId)) {
                $objEstoque->setPedidoId($pedidoId);
            }

            $objEstoque->setOperacao('SAIDA');
            $objEstoque->setData(date('Y-m-d H:i:s'));
            $objEstoque->setQuantidade($quantidade);
            $objEstoque->save();

            try {
                if (Config::get('aviso_estoque_minimo') && $this->getEstoqueAtual() <= $this->getEstoqueMinimo()) {
                    \QPress\Mailing\Mailing::enviarAvisoEstoqueMinimo($this);
                }
            } catch(Exception $e) {}
        } else {
            $arrProdutosCompostos = ProdutoCompostoQuery::create()->findByProdutoId($produto->getId());

            foreach ($arrProdutosCompostos as $objProdutoComposto) {
                /** @var ProdutoComposto $objProdutoComposto */

                $qtdEstoqueDescontar = $objProdutoComposto->getEstoqueQuantidade() * $quantidade;
                $objProdutoCompostoVariacao = $objProdutoComposto->getProdutoVariacao();

                //$objProdutoCompostoVariacao->setEstoqueAtual($objProdutoCompostoVariacao->getEstoqueAtual() - $qtdEstoqueDescontar);
                //$objProdutoCompostoVariacao->save();


                $objEstoque = new EstoqueProduto();
                $objEstoque->setProdutoId($objProdutoCompostoVariacao->getProdutoId());
                $objEstoque->setProdutoVariacaoId($objProdutoCompostoVariacao->getId());
                if (!is_null($pedidoId)) {
                    $objEstoque->setPedidoId($pedidoId);
                }

                $objEstoque->setOperacao('SAIDA');
                $objEstoque->setData(date('Y-m-d H:i:s'));
                $objEstoque->setQuantidade($qtdEstoqueDescontar);
                $objEstoque->save();


                try {
                    if (Config::get('aviso_estoque_minimo') && $objProdutoCompostoVariacao->getEstoqueAtual() <= $objProdutoCompostoVariacao->getEstoqueMinimo()) {
                        \QPress\Mailing\Mailing::enviarAvisoEstoqueMinimo($objProdutoCompostoVariacao);
                    }
                } catch(Exception $e) {}
            }
        }
    }

    /**
     * Aumenta o estoque
     * @param int $quantidade
     */
    public function aumentarEstoque($quantidade)
    {
        $produto = $this->getProduto();

        if (ProdutoPeer::PRODUTO_TAXA_ID == $produto->getId()) {
            return 'taxa_cadastro';
        }

        if ($produto->getTipoProduto() == 'SIMPLES') {
            $this->setEstoqueAtual($this->getEstoqueAtual() + $quantidade);
            $this->save();
        } else {
            $arrProdutosCompostos = ProdutoCompostoQuery::create()->findByProdutoId($produto->getId());

            foreach ($arrProdutosCompostos as $objProdutoComposto) {
                /** @var ProdutoComposto $objProdutoComposto */

                $qtdEstoqueDescontar = $objProdutoComposto->getEstoqueQuantidade() * $quantidade;
                $objProdutoCompostoVariacao = $objProdutoComposto->getProdutoVariacao();

                $objProdutoCompostoVariacao->setEstoqueAtual($objProdutoCompostoVariacao->getEstoqueAtual() + $qtdEstoqueDescontar);
                $objProdutoCompostoVariacao->save();
            }
        }
    }

    private $_nomeCompleto = null;
    public function getProdutoNomeCompleto($preHtmlVariacao = "<br>", $posHtmlVariacao = "", $htmlEntreVariacoes = "<br>")
    {
        if ($this->_nomeCompleto == null) {
            ProdutoPeer::disableSoftDelete();
            $nome = (!is_null($this->getProduto())) ? $this->getProduto()->getNome() : 'N/I';
            $variacoes = $this->getNome($htmlEntreVariacoes);
            if ($variacoes != '') {
                $nome .= $preHtmlVariacao . $variacoes . $posHtmlVariacao;
            }
            $this->_nomeCompleto = $nome;
        }
        return $this->_nomeCompleto;
    }

    private $_nome = null;
    public function getNome($htmlEntreVariacoes = '<br>')
    {
        if ($this->_nome == null) {
            $html = "";
            $br = "";
            foreach ($this->getProdutoVariacaoAtributos() as $pva) { /* @var $pva ProdutoVariacaoAtributo */
                ProdutoAtributoPeer::disableSoftDelete();
                $html .= $br . $pva->getProdutoAtributo()->getDescricao() . ': ' . $pva->getDescricao();
                $br = $htmlEntreVariacoes;
            }
            $this->_nome = $html;
        }
        return $this->_nome;
    }

    public function delete(\PropelPDO $con = null)
    {
        if ($this->countProdutoVariacaoAtributos()) {
            ProdutoVariacaoAtributoQuery::create()->
            filterByProdutoVariacaoId($this->getId())
                ->delete();
        }
        parent::delete($con);
    }

    /**
     * @return float
     */
    public function getValorDesconto()
    {
        return $this->getValorBase() - $this->getValor();
    }

    /**
     * @param bool $withReserved
     * @return int|null
     */

    public function getEstoqueAtual($withReserved = false)
    {
        return EstoqueProdutoPeer::getQuantidadeEstoqueDisponivel($this, null, $withReserved);
    }
   
    /**
     * @param bool $withReserved
     * @return int|null
     */
    public function getEstoqueAtualCD($centroDistribuicao, $withReserved = false)
    {
        return EstoqueProdutoPeer::getQuantidadeEstoqueDisponivel($this, $centroDistribuicao, $withReserved);
    }

    /**
     * @return int|null
     * @throws PropelException
     */

    public function getReservaEstoque()
    {
        return EstoqueProdutoPeer::getQuantidadeSaidaReservadaEstoque($this);
    }

    /**
     * @return int|null
     * @throws PropelException
     */
    public function getSaidaEstoque($centroDistribuicaoId)
    {
        return EstoqueProdutoPeer::getQuantidadeSaidaConfirmadaEstoque($this, $centroDistribuicaoId);
    }

    /**
     * @return int|null
     * @throws PropelException
     */
    public function getEntradaEstoque()
    {
        return EstoqueProdutoPeer::getQuantidadeEntradaEstoque($this);
    }

    /**
     * @return int|null
     * @throws PropelException
     */
    public function getSomaTotalEstoque()
    {
        $centroDistribuicao = CentroDistribuicaoQuery::create()->find();

        $estoque = 0;
        foreach($centroDistribuicao as $cd) :
            $estoque += $this->getEstoqueAtualCD($cd->getId());
        endforeach;

        return $estoque;
    }
}
