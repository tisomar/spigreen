<?php

/**
 * Skeleton subclass for performing query and update operations on the 'QP1_PRODUTO' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ProdutoPeer extends BaseProdutoPeer
{
    CONST ATIVO_SIM = 1;
    CONST ATIVO_NAO = 0;
    CONST DESTAQUE_SIM = 1;
    CONST DESTAQUE_NAO = 0;
    CONST PARTICIPACAO_RESULTADOS_SIM = 1;
    CONST PARTICIPACAO_RESULTADOS_NAO = 0;
    CONST PROPORCAO = '3:4';
    CONST PROPORCAO_V = '3:4';
    CONST PROPORCAO_Q = '1:1';
    CONST PRODUTO_TAXA_ID = 1;
    CONST APLICA_DESCONTO_PLANO_SIM = 1;
    CONST APLICA_DESCONTO_PLANO_NAO = 0;
    CONST FRETE_GRATIS_SIM = 1;
    CONST FRETE_GRATIS_NAO = 0;
    CONST KIT_ESPECIAL_SIM = 1;
    CONST KIT_ESPECIAL_NAO = 0;

    public static function getCategoriaList($defaultLabel = 'Selecione...')
    {
        $options = array(
            null => $defaultLabel
        );

        $root = ProdutoCategoriaQuery::create()->findRoot();

        if ($root instanceof ProdutoCategoria)
        {
            foreach ($root->getBranch() as $object)
            {
                if ($object->isRoot())
                {
                    continue;
                }

                $options[$object->getId()] = '| ' . str_repeat('- - ', $object->getLevel()) . $object->getNome();
            }
        }

        return $options;
    }

    public static function getMarcaList()
    {
        $options = array(
            null => 'Selecione...'
        );

        $collMarca = MarcaQuery::create()->orderByNome()->find();
        foreach ($collMarca as $object)
        {
            $options[$object->getId()] = $object->getNome();
        }

        return $options;
    }
   
    public static function getPlanoList()
    {
        $options = array(
            null => 'Selecione...'
        );

        $collPlano = PlanoQuery::create()->orderById()->find();
        foreach ($collPlano as $object)
        {
            $options[$object->getId()] = $object->getNome();
        }

        return $options;
    }

    public static function getTipoProdutoList()
    {
        $options = array(
            'SIMPLES'   => 'Simples',
            'COMPOSTO'  => 'Composto'
        );


        return $options;
    }

    public static function getAtivoList()
    {

        return array(
            self::ATIVO_SIM => 'Sim',
            self::ATIVO_NAO => 'Não',
        );
    }

    public static function getAtivoDescricao($option)
    {

        $a = self::getAtivoList();

        if (!isset($a[$option]))
        {
            return null;
        }

        return $a[$option];
    }

    public static function getDestaqueList()
    {

        return array(
            self::DESTAQUE_SIM => 'Sim',
            self::DESTAQUE_NAO => 'Não',
        );
    }
    
    public static function getParticipacaoResultadosList()
    {

        return array(
            self::PARTICIPACAO_RESULTADOS_SIM => 'Sim',
            self::PARTICIPACAO_RESULTADOS_NAO => 'Não',
        );
    }

    public static function getDestaqueDescricao($option)
    {

        $a = self::getDestaqueList();

        if (!isset($a[$option]))
        {
            return null;
        }

        return $a[$option];
    }

    /**
     * 
     * @param Criteria $c
     * @param mixed $arguments
     * @return PropelModelPager
     */
    public static function findAll($c = null, $arguments = array())
    {
        $maxPerPage = isset($arguments['max-per-page']) ? $arguments['max-per-page'] : 12;
        $page = isset($arguments['page']) ? $arguments['page'] : 1;
        
        $ordenarPor = !empty($arguments['ordenar-por']) ? $arguments['ordenar-por'] : 'ordem-asc';
        //Este site sempre vai listar os produtos como "preco-desc"
        //Cliente solicitou que o produto com maior preço seja mostrado primeiro (configuração temporária)
        //$ordenarPor = 'preco-desc';

        $query = ProdutoQuery::create(null, $c)
                ->useProdutoVariacaoQuery(null, Criteria::LEFT_JOIN)
                    ->filterByDisponivel(true)
                ->endUse()
                ->setDistinct(true)
                ->filterByCategoriaDisponivel(true);
                // ->filterByDestaque(true);
       
        
        //tratamento da exibição de planos
        if(ClientePeer::isAuthenticad() && !ClientePeer::getClienteLogado(true)->isConsumidorFinal()) {
            $query->filterKits();
        }
       
        switch ($ordenarPor)
        {
            case 'mais-vendidos':
                $query
                    ->useProdutoVendaEstatisticaQuery(null, Criteria::LEFT_JOIN)
                        ->orderByQuantidadeVendida(Criteria::DESC)
                    ->endUse();
                break;

            case 'melhor-avaliados':
                $query
                    ->orderByQuantidadeAvaliacao(Criteria::DESC)
                    ->orderByNotaAvaliacao(Criteria::DESC);
                break;

            case 'nome-asc':
                $query->orderByNome(Criteria::ASC);
                break;

            case 'nome-desc':
                $query->orderByNome(Criteria::DESC);
                break;

            case 'preco-asc':
                $query->orderByValor(Criteria::ASC);
                break;

            case 'preco-desc':
                $query->orderByValor(Criteria::DESC);
                break;

            case 'ordem-asc':
                $query->orderByOrdem(Criteria::ASC);
                break;
        }

        return $query->paginate($page, $maxPerPage);
    }
    
    public static function findOneBy($c) {
        
        return ProdutoQuery::create(null, $c)
                ->setDistinct()
                ->filterByDisponivel(true)
            ->findOne();
        
    }

    /**
     * retorna uma tag select para campo mostrar
     * @param string $strValueSelected
     * @param array $arrOptions
     * @param array $arrAttributtes
     * @return string
     * @deprecated since version 1
     */
    public static function getFormSelectMostrar($strValueSelected, $arrOptions = false, $arrAttributtes = array())
    {
        $arrAttributtes['name'] = isset($arrAttributtes['name']) ? $arrAttributtes['name'] : 'produto[ATIVO]';
        $arrAttributtes['id'] = isset($arrAttributtes['id']) ? $arrAttributtes['id'] : 'ativo';
        $arrAttributtes['title'] = isset($arrAttributtes['title']) ? $arrAttributtes['title'] : 'Indica se &eacute; para mostrar o produto no site ou n&atilde;o';
        $arrAttributtes['class'] = isset($arrAttributtes['tooltip']) ? $arrAttributtes['tooltip'] : 'tooltip';

        if ($arrOptions === false)
        {
            $arrOptions = array(
                Produto::SIM => Produto::getDescConstMostrar(Produto::SIM),
                Produto::NAO => Produto::getDescConstMostrar(Produto::NAO),
            );
        }

        return get_form_select($arrOptions, $strValueSelected, $arrAttributtes);
    }

    /**
     * 
     * @param type $strValueSelected
     * @param type $arrOptions
     * @param type $arrAttributtes
     * @return type
     * @deprecated since version 1
     */
    public static function getFormSelectDestaque($strValueSelected, $arrOptions = false, $arrAttributtes = array())
    {
        $arrAttributtes['name'] = isset($arrAttributtes['name']) ? $arrAttributtes['name'] : 'produto[DESTAQUE]';
        $arrAttributtes['id'] = isset($arrAttributtes['id']) ? $arrAttributtes['id'] : 'ativo';
        $arrAttributtes['title'] = isset($arrAttributtes['title']) ? $arrAttributtes['title'] : 'Indica se o produto aparecer&aacute; como destaque';
        $arrAttributtes['class'] = isset($arrAttributtes['tooltip']) ? $arrAttributtes['tooltip'] : 'tooltip';

        if ($arrOptions === false)
        {
            $arrOptions = array(
                Produto::SIM => Produto::getDescConstMostrar(Produto::SIM),
                Produto::NAO => Produto::getDescConstMostrar(Produto::NAO),
            );
        }

        return get_form_select($arrOptions, $strValueSelected, $arrAttributtes);
    }

    /**
     * Selecionando por relevância os produtos que apareceram mais vezes 
     * em pedidos finalizados junto com o $objProduto enviado
     * 
     * @author Felipe Corrêa
     * @since 26/02/2013
     * 
     * @param Produto $objProduto Produto que é a base para achar os produtos 
     *                            que foram comprados também
     * @param int     $intLimit   Quantidade de produtos que serão retornados
     * 
     * @return PropelCollection Produtos
     */
    public static function getComprouTambem(Produto $objProduto, $intLimit = 8)
    {
        $arrPedidos = array();

        // Selecionando os pedidos finalizados que possuem o produto enviado
        $pedidos = PedidoQuery::create()
                ->filterBySituacao(Pedido::FINALIZADO)
                ->useCarrinhoQuery()
                ->useItemCarrinhoQuery()
                ->filterByProdutoId($objProduto->getId())
                ->endUse()
                ->endUse()
                ->find();

        foreach ($pedidos as $pedido)
        {
            $arrPedidos[] = $pedido->getId();
        }

        // Selecionando todos os produtos que percentem aos pedidos acima
        // e ordenando por ordem de relevância no que tange a quantidade de 
        // vezes que apareceram com o produto enviado
        $produtosComprados = ProdutoQuery::create()
                ->filterById($objProduto->getId(), Criteria::NOT_EQUAL)
                ->filterByAtivo(true)
                ->useItemCarrinhoQuery()
                ->useCarrinhoQuery()
                ->usePedidoQuery()
                ->filterById($arrPedidos)
                ->endUse()
                ->endUse()
                ->endUse()
                ->withColumn('COUNT(' . ProdutoPeer::ID . ')', 'Quantidade')
                ->groupById()
                ->orderBy('Quantidade', Criteria::DESC)
                ->limit($intLimit)
                ->find();

        return $produtosComprados;
    }

    /**
     * Selecionando todas as vendas casadas que possuem o número de produtos 
     * configurado na área administrativa, além de verificar se os produtos são
     * válidos (ativos e não deletados)
     * 
     * @author Felipe Corrêa
     * @since 06/03/2013
     * 
     * @param Produto $objProduto Produto 
     * @param int     $intLimit   Quantidade de vendas casadas que serão retornadas
     * 
     * @return PropelCollection
     */
    public static function getVendasCasadas(Produto $objProduto, $intLimit = 14)
    {
        $objVendasCasadas = VendaCasadaQuery::create()

                // Contando os produtos válidos desta venda casada (ativos e não deletados)
                ->withColumn('(
                                    SELECT COUNT(*) FROM ' . ProdutoPeer::TABLE_NAME . ' produto
                                    INNER JOIN ' . ProdutoVendaCasadaPeer::TABLE_NAME . ' venda_casada
                                    ON (produto.ID = venda_casada.PRODUTO_ID) 
                                    WHERE 
                                        venda_casada.VENDA_CASADA_ID = ' . VendaCasadaPeer::TABLE_NAME . '.ID AND
                                        produto.DATA_EXCLUSAO IS NULL AND
                                        produto.ATIVO = 1
                                    )', 'QtdProdutosValidos')

                // Filtrando por vendas casadas que possuam o produto enviado
                ->useProdutoVendaCasadaQuery()
                ->filterByProdutoId($objProduto->getId())
                ->endUse()

                // Filtrando pelas vendas casadas ativas
                ->filterByAtivo(true)
                ->limit($intLimit)

                // Exibindo apenas as vendas casadas que possuem a quantidade de produtos
                // configurada na área administrativa (se possui menos, significa que algum produto é inválido)
                ->having('QtdProdutosValidos' . Criteria::EQUAL . ConfiguracaoPeer::getInstance()->getQuantidadeProdutosVendaCasada())
                ->find();

        return $objVendasCasadas;
    }

    public static function getBlocksHome() {

        return array(
            'promocoes' => array(
                'query' => 'filterByEmPromocao',
                'breadcrumb' => array(
                    'Produtos em Promoção' => '/produtos/promocoes'
                )
            ),
        );

    }


    public static function formatterProdutoIntegrationBling(Produto $objProduto) {

        $nomeIntegracao = $objProduto->getNomeIntegracao();
        $nome = !empty($nomeIntegracao) && !is_null($nomeIntegracao) ? $nomeIntegracao : $objProduto->getNome();

        return array(
                'codigo' => $objProduto->getId(),
                'descricao' => resumo($nome, '120', ''),
                'descricaoCurta' => $objProduto->getDescricao(),
                'un' => 'un',
                'tipo' => 'P',
                'vlr_unit' => number_format($objProduto->getValor(),'2','.',''),
                'peso_bruto' => number_format($objProduto->getPeso(),'2','.',''),
                'peso_liq' => number_format($objProduto->getPeso(),'2','.',''),
                'origem' => 0,
                'estoque' => $objProduto->getEstoqueAtual(),
                'largura' => $objProduto->getLargura(),
                'altura' => $objProduto->getAltura(),
                'profundidade' => $objProduto->getComprimento(),
                'estoqueMinimo' => $objProduto->getEstoqueMinimo(),

        );

    }

    public static function formatterProdutoWithVariationsIntegrationBling(Produto $objProduto) {

        $arrVariations = $objProduto->getProdutoVariacaos();

        $variation = array();

        foreach ($arrVariations as $objProdutoVariacao){
            $variation[]['variacao'] =
                 array(
                    'nome' => $objProdutoVariacao->getNome(),
                    'codigo' => $objProdutoVariacao->getId(),
                    'vlr_unit' => number_format($objProdutoVariacao->getValor(),'2','.',''),
                    'estoque' => $objProdutoVariacao->getEstoqueAtual()

            );

        }

        $nomeIntegracao = $objProduto->getNomeIntegracao();
        $nome = !empty($nomeIntegracao) && !is_null($nomeIntegracao) ? $nomeIntegracao : $objProduto->getNome();

        return array(
            'produto' => array(
                'codigo' => $objProduto->getId(),
                'descricao' => resumo($nome, '120', ''),
                'descricaoCurta' => $objProduto->getDescricao(),
                'un' => 'un',
                'tipo' => 'P',
                'vlr_unit' => number_format($objProduto->getValor(),'2','.',''),
                'peso_bruto' => number_format($objProduto->getPeso(),'2','.',''),
                'peso_liq' => number_format($objProduto->getPeso(),'2','.',''),
                'origem' => 0,
                'estoque' => $objProduto->getEstoqueAtual(),
                'largura' => $objProduto->getLargura(),
                'altura' => $objProduto->getAltura(),
                'profundidade' => $objProduto->getComprimento(),
                'estoqueMinimo' => $objProduto->getEstoqueMinimo(),
                'variacoes' => $variation,

            )
        );

    }

    public static function formatterProdutoServiceIntegrationBling(Produto $objProduto) {

        $nomeIntegracao = $objProduto->getNomeIntegracao();
        $nome = !empty($nomeIntegracao) && !is_null($nomeIntegracao) ? $nomeIntegracao : $objProduto->getNome();

        return array(
            'produto' => array(
                'codigo' => $objProduto->getId().'S',
                'descricao' => resumo($nome.'S', '120', ''),
                'descricaoCurta' => $objProduto->getDescricao(),
                'tipo' => 'S',
                'un' => 'un',
                'vlr_unit' => number_format($objProduto->getValorServico(),'2','.',''),
                'origem' => 0,

            )
        );

    }

    public static function formatterProdutoWithVariationsServiceIntegrationBling(Produto $objProduto) {

        $arrVariations = $objProduto->getProdutoVariacaos();

        $variation = array();

        foreach ($arrVariations as $objProdutoVariacao){
            $variation[]['variacao'] =
                array(
                    'nome' => $objProdutoVariacao->getNome(),
                    'codigo' => $objProdutoVariacao->getId(),
                    'vlr_unit' => number_format($objProduto->getValorServico(),'2','.',''),
                    'estoque' => $objProdutoVariacao->getEstoqueAtual()

                );

        }

        $nomeIntegracao = $objProduto->getNomeIntegracao();
        $nome = !empty($nomeIntegracao) && !is_null($nomeIntegracao) ? $nomeIntegracao : $objProduto->getNome();

        return array(
            'produto' => array(
                'codigo' => $objProduto->getId().'S',
                'descricao' => resumo($nome.'S', '120', ''),
                'descricaoCurta' => $objProduto->getDescricao(),
                'un' => 'un',
                'tipo' => 'S',
                'vlr_unit' => number_format($objProduto->getValorServico(),'2','.',''),
                'peso_bruto' => number_format($objProduto->getPeso(),'2','.',''),
                'origem' => 0,
                'estoque' => $objProduto->getEstoqueAtual(),
                'largura' => $objProduto->getLargura(),
                'altura' => $objProduto->getAltura(),
                'profundidade' => $objProduto->getComprimento(),
                'estoqueMinimo' => $objProduto->getEstoqueMinimo(),
                'variacoes' => $variation,

            )
        );

    }

    public static function getProdutoSimplesList() {
        $c = ProdutoQuery::create()->filterByTipoProduto('SIMPLES')->select(array('Id', 'Nome'))->orderById()->find()->toArray();
        return array_column($c, 'Nome', 'Id');
    }

    public static function getAplicaDescontoPlanoList()
    {
        return array(
            self::APLICA_DESCONTO_PLANO_SIM => 'Sim',
            self::APLICA_DESCONTO_PLANO_NAO => 'Não',
        );
    }

    public static function getFreteGratisList()
    {
        return array(
            self::FRETE_GRATIS_SIM => 'Sim',
            self::FRETE_GRATIS_NAO => 'Não',
        );
    }

    public static function getIsKitEspecialList()
    {
        return array(
            self::KIT_ESPECIAL_SIM => 'Sim',
            self::KIT_ESPECIAL_NAO => 'Não',
        );
    }
}
