<?php

/**
 * Skeleton subclass for representing a row from the 'QP1_PRODUTO' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Produto extends BaseProduto implements
    \QualityPress\QCommerce\Component\Association\Model\AssociableInterface
{

    /**
     * @var ProdutoVariacao
     */
    protected $aProdutoVariacao;

    public function myValidate(&$erros, $columns = null)
    {
        /**
         * O sistema não permite o cadastro de refência duplicada considerando apenas as variações master.
         * As variações "não master" podem ser ou possuir informações de refência duplicadas.
         */
        $isDuplicatedSku = count(ProdutoVariacaoQuery::create()
                ->filterBySku($this->getSku())
                ->filterByProdutoId($this->getId(), Criteria::NOT_EQUAL)
                ->filterByIsMaster(true)
                ->find()) > 0;

        if ($isDuplicatedSku) {
            $erros['sku'] = 'Já há um produto com a referência <b>' . $this->getSku() . '</b> cadastrada no sistema';
        }

        if ($this->hasVariacoes() && $this->getTipoProduto() == 'COMPOSTO') {
            $erros['tipo_produto'] = "Produto com Variações, delete as variações para fazer um produto composto.";
        }

        return parent::myValidate($erros, $columns);
    }

    /**
     * Retorna a variação master do produto
     *
     * @return ProdutoVariacao
     */
    public function getProdutoVariacao()
    {

        if (is_null($this->aProdutoVariacao)) {
            $this->aProdutoVariacao = ProdutoVariacaoQuery::create()
                ->filterByIsMaster(true)
                ->filterByProdutoId($this->getId())
                ->findOne();
        }

        if (is_null($this->aProdutoVariacao)) {
            $this->aProdutoVariacao = new ProdutoVariacao();
            $this->aProdutoVariacao->setIsMaster(true);
            $this->aProdutoVariacao->setProduto($this);
        }

        return $this->aProdutoVariacao;
    }

    /**
     * @see ProdutoVariacao -> getSku()
     */
    public function getSku()
    {
        return $this->getProdutoVariacao()->getSku();
    }

    /**
     * @see ProdutoVariacao -> getValorBase()
     */
    public function getValorBase()
    {
        return $this->getProdutoVariacao()->getValorBase();
    }

    /**
     * @see ProdutoVariacao -> getValorPromocional()
     */
    public function getValorPromocional()
    {
        return $this->getProdutoVariacao()->getValorPromocional();
    }

    /**
     * @see ProdutoVariacao -> getValorPromocional()
     */
    public function getValorDistribuidor()
    {
        return $this->getProdutoVariacao()->getValorDistribuidor();
    }

    /**
     * @see ProdutoVariacao -> getEstoqueAtual()
     */
    public function getEstoqueAtual()
    {
        return $this->getProdutoVariacao()->getEstoqueAtual();
    }

    /**
     * @see ProdutoVariacao -> getEstoqueMinimo()
     */
    public function getEstoqueMinimo()
    {
        return $this->getProdutoVariacao()->getEstoqueMinimo();
    }

    /**
     * @see ProdutoVariacao -> getDisponivel()
     */
    public function getDisponivel()
    {
        return $this->getProdutoVariacao()->getDisponivel();
    }

    /**
     * @see ProdutoVariacao -> getValor()
     */
    public function getValor()
    {
        return $this->getProdutoVariacao()->getValor();
    }

    /**
     * Retorna TRUE se o produto estiver com o valor de promoção preenchido.
     *
     * @return boolean true ou false
     */
    public function isPromocao()
    {
        return $this->getProdutoVariacao()->isPromocao() && $this->isDisponivel();
    }

    /**
     * Retorna uma string contendo a quantidade máxima de parcelamento para o valor do produto e o valor da parcela.
     *
     * @return string
     */
    public function getDescricaoParcelado()
    {
        return get_descricao_valor_parcelado($this->getValor(), $this->getParcelamentoIndividual());
    }

    /**
     * Calcula o valor do produto com base na quantidade
     *
     * @param integer $quantidade Quantidade a ser calculada
     *
     * @return float
     */
    public function calculaValor($quantidade = 1)
    {
        return $quantidade * $this->getValor();
    }

    /**
     * Retorna o numero de parcelas disponíveis para o produto
     * @return int
     */
    public function getNumeroMaximoParcelas($valor = null)
    {
        if (is_null($valor)) {
            $valor = $this->calculaValor();
        }
        return getParcelasByValor($valor);
    }

    /**
     * Retorna o percentual de desconto do produto
     *
     * @return int
     */
    public function getPercentualDesconto()
    {

        $percentualDesconto = 0;

        if ($this->isPromocao()) {
            $valorBase = $this->getValorBase();
            $valorPromocional = (ClientePeer::isAuthenticad()
                && !is_null(ClientePeer::getClienteLogado(true)->getPlano())
                && $this->getValorDistribuidor() > 0)
                ? $this->getValorDistribuidor()
                : $this->getValorPromocional();
            $percentualDesconto = round(100 - (($valorPromocional * 100) / $valorBase));
        }

        return $percentualDesconto;
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
     * @throws Exception
     */
    public function getValorFidelidade()
    {
        return $this->getProdutoVariacao()->getValorFidelidade();
    }

     /**
     * Retorna uma string contendo a quantidade máxima de parcelamento para o valor do produto e o valor da parcela.
     *
     * @return string
     */
    public function getDescricaoParceladoFidelidade()
    {
        return get_descricao_valor_parcelado($this->getValorFidelidade()[0], $this->getParcelamentoIndividual());
    }

    /**
     * Retorna a primeira imagem do produto.
     *
     * @return Foto
     */
    private $foto_principal = null;
    public function getImagemPrincipal()
    {
        if ($this->foto_principal == null) {
            $this->foto_principal = FotoQuery::create()
                ->filterByProdutoId($this->getId())
                ->orderByOrdem()
                ->orderByCor()
                ->orderById(Criteria::ASC)
                ->findOneOrCreate();
        }
        return $this->foto_principal;
    }

    public function getFotosByCor($cor)
    {

        $collFotos = FotoQuery::create()
            ->joinWith(ProdutoPeer::getOMClass())
            ->filterByProdutoId($this->getId())
            ->limit(Config::get('produto_limite_imagens'))
            ->filterByCor($cor)
            ->orderByOrdem()
            ->orderByCor()
            ->find();

        if (count($collFotos) == 0) {
            $collFotos = FotoQuery::create()
                ->joinWith(ProdutoPeer::getOMClass())
                ->filterByProdutoId($this->getId())
                ->limit(Config::get('produto_limite_imagens'))
                ->orderByOrdem()
                ->orderByCor()
                ->find();
        }

        return $collFotos;
    }

    /**
     * IMPORTANTE: NÃO REMOVER ESTA FUNÇÃO, ELA É RESPONSÁVEL POR SOBRESCREVER
     * O PATTERN DE CRIAÇÃO DE SLUG DO PROPEL, CRIANDO NOMES CORRETOS
     *
     * EX.: "teste / produto", ficará "teste-produto" e o propel faria
     * teste-/-produto, fazendo com que nossas URLs amigáveis não funcionem e
     * retorne erro 404.
     *
     * Cleanup a string to make a slug of it
     * Removes special characters, replaces blanks with a separator, and trim it
     *
     * @param     string $text      the text to slugify
     * @param     string $separator the separator used by slug
     * @return    string             the slugified text
     */
    protected static function cleanupSlugPart($slug, $replacement = '-')
    {
        setlocale(LC_ALL, 'pt_BR.ISO-8859-1');

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', trim($slug));
        if (!$clean) {
            $clean = $slug;
        }
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $replacement, $clean);

        return $clean;
    }

    public function getUrlDetalhes()
    {
        return ROOT_PATH . '/produtos/detalhes/' . escape($this->getKey()) . '/';
    }

    /**
     * Retorna a categoria do produto
     * @return ProdutoCategoria $objCategoria
     */
    public function getCategoria()
    {
        $objCategoria = ProdutoCategoriaQuery::create()
            ->joinCategoria()
            ->filterByProdutoId($this->getId())
            ->findOne();

        if ($objCategoria instanceof ProdutoCategoria && $objCategoria->getCategoria() instanceof Categoria) {
            return $objCategoria->getCategoria();
        }
        return new Categoria();
    }

    /**
     * Retorna aleatoriamente os produtos que estão relacionados ao produto atual
     * com base na categoria ao qual os produtos pertencem
     *
     * @param $intLimit Quantidade de produtos relacionados que serão retornados
     *
     * @return PropelCollection
     */
    public function getProdutosRelacionados($intLimit = 8)
    {

        return ProdutoQuery::create()
            ->useProdutoCategoriaQuery()
            ->filterByCategoriaId($this->getProdutoCategorias(ProdutoCategoriaQuery::create()->select(array('CategoriaId')))->toArray())
            ->endUse()
            ->filterByDisponivel(true)
            ->filterById($this->getId(), Criteria::NOT_EQUAL)
            ->limit($intLimit)
            ->groupById()
            ->addAscendingOrderByColumn('RAND()')
            ->find();
    }

    /**
     * Retorna com o cálculo de desconto para exibir na listagem de produtos
     * @return String
     */
    public function exibePreco()
    {
        $valor = '';

        $valor .= '<span class="old-price">';
        if ($this->isPromocao()) {
            $valor .= 'De R$ ' . $this->getValorFormatado();
        }
        $valor .= '</span>';
        $valor .= 'Por R$ ' . $this->getValorComDescontoFormatado();

        return $valor;
    }

    /*
     * Retorna a quantidade de estoque total do produto
     * @return int $estoque
     */

    public function getEstoqueTotal()
    {
        $estoque = 0;

        if ($this->hasVariacoes()) {
            foreach ($this->getVariacoes() as $objVariacao) {
                $estoque += $objVariacao->getQtdEstoque();
            }
        } else {
            $estoque = $this->getQtdEstoque();
        }

        return $estoque;
    }

    /**
     * Retorna descricao para constanstes de mostrar
     * @param string $strMostrar
     * @return string
     */
    public static function getDescConstMostrar($strMostrar)
    {
        switch ($strMostrar) {
            case self::SIM:
                $strRet = 'Sim';
                break;
            case self::NAO:
                $strRet = 'N&atilde;o';
                break;
            default:
                $strRet = '';
                break;
        }
        return $strRet;
    }

    /**
     * Retorna um array com a quantidade de avaliações por nota
     *
     * @return array
     */
    public function getArrNotas()
    {
        $qtdNotas = array(5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0);

        foreach ($this->getProdutoComentarios(ProdutoComentarioPeer::filtroAtivos()) as $objComentario) {
            $qtdNotas[$objComentario->getNota()] = $qtdNotas[$objComentario->getNota()] + 1;
        }

        return $qtdNotas;
    }

    /**
     * Retorna se o produto está disponível ou não
     * @return boolean true ou false
     */
    public function isDisponivel()
    {

        /** Retirado essa validação a pedido do cliente 09/05/18 */
        // if ($this->isKitAdesao()) {
        //      return true; //kits estão sempre disponiveis, independente do valor/estoque.
        // }

        // METODO ANTIGO PEGAVA ESTOQUE INDIVIDUAL,
        // if ($this->hasVariacoes()) {
        //     $countProdutoVariacao = ProdutoVariacaoQuery::create()
        //         ->filterByProdutoId($this->getId())
        //         ->filterByIsMaster(false)
        //         ->filterByDisponivel(true)
        //         ->filterByEstoqueAtual(array('min' => 0))
        //         ->filterByValorBase(array('min' => 0))
        //         ->find();

        //     return count($countProdutoVariacao) > 0;
        // } else {
        //     return $this->getEstoqueAtual() > 0 && $this->getValor() > 0;
        // }
        if ($this->hasVariacoes()) :
            $variacoes = ProdutoVariacaoQuery::create()
                ->filterByProdutoId($this->getId())
                ->filterByIsMaster(false)
                ->filterByDisponivel(true)
                ->find();

            foreach ($variacoes as $variacao) :
                if ($variacao->getSomaTotalEstoque() > 0 && $variacao->getProduto()->getValor() > 0) :
                    return true;
                endif;
            endforeach;

            return false;
        else:
            return $this->getProdutoVariacao()->getSomaTotalEstoque() > 0 && $this->getValor() > 0;
        endif;
    }

    /**
     * Retorna se o produto possui variações
     * @return boolean true or false
     */
    public function hasVariacoes()
    {
        return count($this->getProdutoAtributos()) > 0;
    }

    public function getLabelForSelectTag()
    {
        return $this->getReferencia() . " - " . $this->getNome();
    }

    public function getProdutoComentarioAprovado($limit = 5)
    {
        return ProdutoComentarioQuery::create()
            ->filterByStatus(ProdutoComentario::STATUS_APROVADO)
            ->filterByProduto($this)
            ->orderByData(Criteria::DESC)
            ->limit($limit)
            ->find();
    }

    public function redefinirAtributos()
    {

        $atributos = ProdutoAtributoQuery::create()
            ->filterByProdutoId($this->getId())
            ->find();

        /* @var $atributo ProdutoAtributo */
        foreach ($atributos as $atributo) {
            $atributo->getProdutoVariacaoAtributos()->delete();
            $atributo->delete();
        }
    }

    function delete(PropelPDO $con = null)
    {
        $this->getFotos()->delete();
        return parent::delete($con);
    }

    public function getThumb($strArgs, $arrAtributtes = array(), $boolUseImagemPadrao = true)
    {
        return $this->getImagemPrincipal()->getThumb($strArgs, $arrAtributtes, $boolUseImagemPadrao);
    }

    public function getUrlImageResize($strArgs)
    {
        return $this->getImagemPrincipal()->getUrlImageResize($strArgs);
    }

    /**
     * Atualiza a nota média e a quantidade de avaliações aprovadas para o produto.
     *
     * @return $this
     */
    public function updateAvaliacao()
    {

        $infoComentarios = ProdutoComentarioQuery::create()
            ->select(array('QTD_COMENTARIO', 'MEDIA_NOTA'))
            ->withColumn('COUNT(Id)', 'QTD_COMENTARIO')
            ->withColumn('AVG(Nota)', 'MEDIA_NOTA')
            ->filterByProdutoId($this->getId())
            ->filterByStatus(ProdutoComentario::STATUS_APROVADO)
            ->findOne();

            // var_dump($infoComentarios);
            // die;

        if ($infoComentarios['QTD_COMENTARIO'] > 0) {
            $this   
                ->setNotaAvaliacao(round($infoComentarios['MEDIA_NOTA'], 3))
                ->setQuantidadeAvaliacao($infoComentarios['QTD_COMENTARIO'])
                ->save();
        }

        return $this;
    }

    /**
     * @return \QPress\Frete\Package\Package
     */
    public function generatePackage($cep)
    {
        $package = new \QPress\Frete\Package\Package();

        $package->addItem(new \QPress\Frete\Package\PackageItem(
            $this->getProdutoVariacao()->getId(),
            $this->getPeso(),
            $this->getAltura(),
            $this->getComprimento(),
            $this->getLargura(),
            1,
            $this->getValor()
        ));

        // Adiciona os parametros de origem e destino
        $package->setClient(new \QPress\Frete\Package\PackageClient(
            Config::get('cep_origem'),   // cep de origem
            $cep      // cep de destino
        ));

        return $package;
    }

    /**
     * {@inheritdoc}
     */
    public function postDelete(PropelPDO $con = null)
    {
        // Necessário efetuar a exclusão das variações
        $this->getProdutoVariacaos()->delete();

        parent::postDelete($con);
    }

    public function setTags($v)
    {

        if (!is_array($v)) {
            if (is_string($v)) {
                $v = array_map('trim', explode(",", $v));
            }
        }

        return parent::setTags($v);
    }

    /**
     * Busca a variação padrão do produto com base na configuração definida pelo administrador.
     *
     * @return ProdutoVariacao
     */
    public function getVariacaoPadrao()
    {

        /**
         * Alias: produto_variacao.selecao_automatica
         *
         * Até o momento 01.02.2016 16:26 as opções disponíveis são:
         * {    "0": "Não selecionar nenhuma opção automaticamente",
         *      "1": "Selecionar a primeira variação disponível",
         *      "2": "Selecionar a variação mais vendida"
         * }
         */

        $objProdutoVariacao = null;

        // Busca por variação mais vendida
        if (Config::get('produto_variacao.selecao_automatica') == 2) {
            $objProdutoVariacao = ProdutoVariacaoQuery::create()
                ->filterByProdutoId($this->getId())
                ->filterByDisponivel(true)
                ->filterByEstoqueAtual(array('min' => 1))
                ->filterByIsMaster(0)
                ->useEstatisticaVendaProdutoVariacaoQuery(null, Criteria::LEFT_JOIN)
                ->orderByQuantidadeVendida(Criteria::DESC)
                ->endUse()
                ->orderById()
                ->findOne();
        } elseif (Config::get('produto_variacao.selecao_automatica') == 3) {
            $objProdutoVariacao = ProdutoVariacaoQuery::create()
                ->filterByProdutoId($this->getId())
                ->filterByDisponivel(true)
                ->filterByEstoqueAtual(array('min' => 1))
                ->filterByIsMaster(0)
                ->filterByIsPadrao(true)
                ->orderById()
                ->findOne();
        }

        if ($objProdutoVariacao == null) {
            $objProdutoVariacao = ProdutoVariacaoQuery::create()
                ->filterByProdutoId($this->getId())
                ->filterByDisponivel(true)
                ->filterByEstoqueAtual(array('min' => 1))
                ->filterByIsMaster(0)
                ->findOne();
        }

        if ($objProdutoVariacao == null) {
            $objProdutoVariacao = $this->getProdutoVariacao();
        }

        return $objProdutoVariacao;
    }

    /**
     * @return float
     */
    public function getValorDesconto()
    {
        return $this->getProdutoVariacao()->getValorDesconto();
    }

    /**
     * Test if column PLANO_ID is null or not.
     *
     * @return bool
     */
    public function isKitAdesao()
    {
        return $this->getPlanoId() !== null;
    }


    /**
     *
     * @return bool
     */
    public function isMensalidade()
    {
        return (bool)parent::getMensalidade();
    }


    /**
     *
     * @return Plano|null
     * @throws PropelException
     */
    public function getPlano()
    {
        return $this->getPlanoRelatedByPlanoId();
    }

    /**
     * Retorna TRUE se o produto estiver com o valor de promoção preenchido.
     *
     * @return boolean true ou false
     */
    public function isProdutoComposto()
    {
        return $this->getTipoProduto() == 'COMPOSTO';
    }

    /**
     * Retorna TRUE se o produto estiver com o valor de promoção preenchido.
     *
     * @return boolean true ou false
     */
    public function isProdutoSimples()
    {
        return $this->getTipoProduto() == 'SIMPLES';
    }

    public function setValorServico($v)
    {
        if (!is_numeric($v)) {
            $v = str_replace(array('R$', ' '), null, $v);
            $v = format_number($v, UsuarioPeer::LINGUAGEM_INGLES);
        }

        return parent::setValorServico($v);
    }

    public function setValorCusto($v)
    {
        if (!is_numeric($v)) {
            $v = str_replace(array('R$', ' '), null, $v);
            $v = format_number($v, UsuarioPeer::LINGUAGEM_INGLES);
        }

        return parent::setValorCusto($v);
    }
}
