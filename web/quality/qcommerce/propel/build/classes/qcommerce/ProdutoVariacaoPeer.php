<?php

/**
 * Skeleton subclass for performing query and update operations on the 'qp1_produto_variacao' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ProdutoVariacaoPeer extends BaseProdutoVariacaoPeer
{
    /**
     * Opções para o campo "DISPONIVEL"
     */
    CONST DISPONIVEL_SIM = 1;
    CONST DISPONIVEL_NAO = 0;

    /**
     * Adiciona um produto variação ao carrinho.
     *
     * @param \QPress\Container\Container $container
     * @param int $produto_variacao_id
     * @param int $quantidade
     * @return bool|PedidoItem PedidoItem adicionado/atualizado ou false quando não for possível adicionar.
     */
    public static function addProdutoVariacaoToCart($container, $produto_variacao_id, $quantidade) {
        
        $objProdutoVariacao = ProdutoVariacaoQuery::create()->findOneById($produto_variacao_id);
        
        $objProduto = $objProdutoVariacao->getProduto();

        if($objProduto->isProdutoSimples()) {
            if (is_null($objProdutoVariacao)) {
                FlashMsg::danger('Desculpe, mas uma variação que você tentou adicionar no carrinho não está disponível no momento.');
                return false;
            }

            if (!$objProdutoVariacao->isDisponivel()) {
                FlashMsg::info(sprintf('O produto <b>%s</b> não está disponível para venda.',
                    $objProdutoVariacao->getProdutoNomeCompleto(' &minus; ')
                ));
                return false;
            }

            // Define a quantidade a ser adicionada no carrinho
            if ($quantidade < 1) {
                return false;
            }

            // Valida o estoque.

            if ($objProdutoVariacao->getSomaTotalEstoque() < $quantidade) {
                FlashMsg::info(sprintf('O produto <b>%s</b> possui apenas <b>%s</b> no estoque.',
                    $objProdutoVariacao->getProdutoNomeCompleto(' &minus; '),
                    plural($objProdutoVariacao->getSomaTotalEstoque(), '%s item', '%s itens')
                ));

                $quantidade = $objProdutoVariacao->getSomaTotalEstoque();

            }
            
            // Verifica se existe um item no carrinho com esta variação e adiciona caso não exista.
            $item = PedidoItemQuery::create()
                ->filterByProdutoVariacaoId($objProdutoVariacao->getId())
                ->filterByPedidoId($container->getCarrinhoProvider()->getCarrinho()->getId())
                ->filterByPlanoId(null, Criteria::ISNULL)
                ->findOneOrCreate();

            $item->setQuantidade($quantidade);
            $clienteLogado = ClientePeer::getClienteLogado(true);
            $planoCliente =  $clienteLogado ? $clienteLogado->getPlano() : null;
            if ($clienteLogado && $planoCliente && $objProdutoVariacao->getProduto()->getAplicaDescontoPlano()) :
                $item->setValorUnitario($objProdutoVariacao->getValorFidelidade()[0]);
            else :
                $item->setValorUnitario($objProdutoVariacao->getValor());
            endif;
            $item->setValorPontosUnitario($objProduto->getValorPontos());
            $item->setPeso($objProduto->getPeso());
            $item->setValorCusto($objProduto->getValorCusto());

            $container->getCarrinhoProvider()->getCarrinho()->addItem($item);
        } else {
            if (is_null($objProdutoVariacao)) {
                FlashMsg::danger('Desculpe, mas uma variação que você tentou adicionar no carrinho não está disponível no momento.');
                return false;
            }

            if (!$objProdutoVariacao->isDisponivel()) {
                FlashMsg::info(sprintf('O produto <b>%s</b> não está disponível para venda.',
                    $objProdutoVariacao->getProdutoNomeCompleto(' &minus; ')
                ));
                return false;
            }

            if (!$objProdutoVariacao->getIsMaster()) {
                FlashMsg::info(sprintf('O produto <b>%s</b> não está disponível para venda.',
                    $objProdutoVariacao->getProdutoNomeCompleto(' &minus; ')
                ));
                return false;
            }

            // Define a quantidade a ser adicionada no carrinho
            if ($quantidade < 1) {
                return false;
            }

            // Valida o estoque.
            $arrProdutoCompostos = ProdutoCompostoQuery::create()->findByProdutoId($objProduto->getId());

            $menorQtdEstoqueComposto = null;

            foreach ($arrProdutoCompostos as $objProdutoComposto) {
                /** @var $objProdutoComposto ProdutoComposto */

                $objProdutoEstoque = $objProdutoComposto->getProdutoVariacao();
                $qtdRequisitadaEstoque = $quantidade * $objProdutoComposto->getEstoqueQuantidade();

                $qtdEstoque = $objProdutoEstoque->getSomaTotalEstoque();

                if ($qtdEstoque < $qtdRequisitadaEstoque && $qtdEstoque > 0) {
                    $estoqueAtual = floor($qtdEstoque / $objProdutoComposto->getEstoqueQuantidade());
                    if (is_null($menorQtdEstoqueComposto) || $menorQtdEstoqueComposto > $estoqueAtual) {
                        $menorQtdEstoqueComposto = $estoqueAtual;
                    }
                } elseif($qtdEstoque < $qtdRequisitadaEstoque && $qtdEstoque <= 0){
                    $menorQtdEstoqueComposto = 0;
                }

            }

            if(is_numeric($menorQtdEstoqueComposto)){

                FlashMsg::info(sprintf('O produto <b>%s</b> possui apenas <b>%s</b> no estoque.',
                    $objProdutoVariacao->getProdutoNomeCompleto(' &minus; '),
                    plural($menorQtdEstoqueComposto, '%s item', '%s itens')
                ));

                $quantidade = $menorQtdEstoqueComposto;

//                if ($menorQtdEstoqueComposto > 0) {
//
//                    FlashMsg::aviso('Produto ' . $objProduto->getNome() . ' possui apenas ' . $menorQtdEstoqueComposto . ' unidades em estoque.');
//                    CarrinhoOperator::getInstance()->updateItemQuantidade($this, $objItemCarrinho, $menorQtdEstoqueComposto);
//                    $errosEstoque = true;
//                } elseif ($menorQtdEstoqueComposto <= 0) {
//
//                    FlashMsg::aviso('Produto ' . $objProduto->getNome() . ' não possui estoque e foi removido de seu carrinho de compras.');
//                    $arrItensDelete[$objProduto->getId()] = $objItemCarrinho;
//                    //CarrinhoOperator::getInstance()->deleteItemCarrinho($this, $objItemCarrinho);
//                    $errosEstoque = true;
//                }
            }

            // Verifica se existe um item no carrinho com esta variação e adiciona caso não exista.
            $item = PedidoItemQuery::create()
                ->filterByProdutoVariacaoId($objProdutoVariacao->getId())
                ->filterByPedidoId($container->getCarrinhoProvider()->getCarrinho()->getId())
                ->findOneOrCreate();

            $item->setQuantidade($quantidade);
            $item->setValorCusto($objProdutoVariacao->getProduto()->getValorCusto());
            $item->setValorUnitario($objProdutoVariacao->getValor());
            $item->setValorPontosUnitario($objProduto->getValorPontos());
            $item->setPeso($objProdutoVariacao->getProduto()->getPeso());

            $container->getCarrinhoProvider()->getCarrinho()->addItem($item);
        }

        return $item;

    }

    public static function getCurrentQtyCartProduct($container, $produto_variacao_id) {
        $objProdutoVariacao = ProdutoVariacaoQuery::create()->findOneById($produto_variacao_id);

        $item = PedidoItemQuery::create()
                ->filterByProdutoVariacaoId($objProdutoVariacao->getId())
                ->filterByPedidoId($container->getCarrinhoProvider()->getCarrinho()->getId())
                ->filterByPlanoId(null, Criteria::ISNULL)
                ->findOne();
        
        if (!empty($item)) {
            return $item->getQuantidade();
        }
        return 0;
    }

    public static function addProdutoTaxaCadastroToCart($container, Produto $objProdutoTaxa) {

        $objProdutoVariacao = $objProdutoTaxa->getProdutoVariacao();


        if (is_null($objProdutoVariacao)) {
            FlashMsg::danger('Desculpe, mas uma variação que você tentou adicionar no carrinho não está disponível no momento.');
            return false;
        }

        if (!$objProdutoVariacao->getDisponivel()) {
            FlashMsg::info(sprintf('O produto <b>%s</b> não está disponível para venda.',
                $objProdutoVariacao->getProdutoNomeCompleto(' &minus; ')
            ));
            return false;
        }


        // Verifica se existe um item no carrinho com esta variação e adiciona caso não exista.
        $item = PedidoItemQuery::create()
            ->filterByProdutoVariacaoId($objProdutoVariacao->getId())
            ->filterByPedidoId($container->getCarrinhoProvider()->getCarrinho()->getId())
            ->findOneOrCreate();

        $item->setQuantidade(1);
        $item->setValorUnitario($objProdutoVariacao->getValor());
        $item->setPeso(0);
        $item->setValorCusto($objProdutoVariacao->getProduto()->getValorCusto());

        $container->getCarrinhoProvider()->getCarrinho()->addItem($item);

        return $item;

    }

    /**
     * @param $objProdutoVariacao
     * @param $quantidade
     * @return bool|float|int|null
     * @throws PropelException
     */
    public static function checkEstoqueProdutoComposto($objProdutoVariacao, $quantidade) {


        $objProduto = $objProdutoVariacao->getProduto();

        /** @var $objProduto Produto */

        if($objProduto->isProdutoComposto()) {

            // Valida o estoque.
            $arrProdutoCompostos = ProdutoCompostoQuery::create()->findByProdutoId($objProduto->getId());

            $menorQtdEstoqueComposto = null;

            foreach ($arrProdutoCompostos as $objProdutoComposto) {
                /** @var $objProdutoComposto ProdutoComposto */

                $objProdutoEstoque = $objProdutoComposto->getProdutoVariacao();
                $qtdRequisitadaEstoque = $quantidade * $objProdutoComposto->getEstoqueQuantidade();

                $qtdEstoque = $objProdutoEstoque->getSomaTotalEstoque();

                if ($qtdEstoque < $qtdRequisitadaEstoque && $qtdEstoque > 0) {
                    $estoqueAtual = floor($qtdEstoque / $objProdutoComposto->getEstoqueQuantidade());
                    if (is_null($menorQtdEstoqueComposto) || $menorQtdEstoqueComposto > $estoqueAtual) {
                        $menorQtdEstoqueComposto = $estoqueAtual;
                    }
                } elseif($qtdEstoque < $qtdRequisitadaEstoque && $qtdEstoque <= 0){
                    $menorQtdEstoqueComposto = 0;
                }

            }

            if(is_null($menorQtdEstoqueComposto)){

                return true;
            } else {
                return $menorQtdEstoqueComposto;
            }

        }
        return false;

    }

    /**
     * @return array
     */
    public static function getValueSet($column)
    {

        switch ($column)
        {
            case ProdutoVariacaoPeer::DISPONIVEL:

                $response = array(
                    self::DISPONIVEL_SIM => 'Sim',
                    self::DISPONIVEL_NAO => 'Não',
                );

                break;

            default:
                throw new Exception('variable $column not found!');
        }

        return $response;
    }

    /**
     * Busca os valores que faltam ser preenchidos na busca pelas variações.
     *
     * @param mixed $combinacao
     * @param integer $produto_id
     *
     * @return mixed
     */
    public static function retrieveByCombinacao($combinacao, $produto_id)
    {

        $templateSearch = array('{{alias}}', '{{atributo.descricao}}', '{{atributo.id}}');


        $con = Propel::getConnection();

        $query = "
            SELECT pv.ID as PRODUTO_VARIACAO_ID
            FROM qp1_produto_variacao pv
        ";

        foreach ($combinacao as $_atributo_id => $_atributo_descricao)
        {
            $alias              = sprintf('p%s', $_atributo_id);
            $templateReplace    = array($alias, $_atributo_descricao, $_atributo_id);

            $subject = "
                JOIN qp1_produto_variacao_atributo as {{alias}}
                    ON pv.ID = {{alias}}.PRODUTO_VARIACAO_ID
                    AND {{alias}}.DESCRICAO LIKE '{{atributo.descricao}}' 
                    AND {{alias}}.PRODUTO_ATRIBUTO_ID = {{atributo.id}}
            ";

            $query .= str_replace($templateSearch, $templateReplace, $subject);
        }

        $query .= "
            WHERE pv.PRODUTO_ID = " . $produto_id . "
            AND pv.DATA_EXCLUSAO IS NULL
            AND pv.IS_MASTER = false
        ";

        $stmt = $con->prepare($query);
        $stmt->execute();
        $response = $stmt->fetch();

        if ($response !== false)
        {
            return $response[0];
        }

        return false;
    }
}
