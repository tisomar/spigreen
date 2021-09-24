<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Skeleton subclass for representing a row from one of the subclasses of the 'qp1_pedido' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Carrinho extends Pedido
{

    protected $frete_manager;

    public function __construct()
    {
        parent::__construct();
        $this->setClassKey(PedidoPeer::CLASSKEY_2);
    }

    public function setFreteManager($v)
    {
        $this->frete_manager = $v;
        return $this;
    }

    public function getFreteManager()
    {
        return $this->frete_manager;
    }

    /**
     * Limpa as informações de frete do carrinho.
     *
     * @param bool $save FALSE para não comitar as informações para o banco de dados.
     * @return $this
     */
    public function resetFrete($save = true)
    {
        $this->setFrete(null);
        $this->setFretePrazo(null);
        $this->setValorEntrega(null);
        if ($this->getPedidoRetiradaLoja()) {
            $this->getPedidoRetiradaLoja()->delete();
        }
        if ($save) {
            $this->save();
            Config::getContainer()->getCarrinhoProvider()->restoreCart($this);
        }
        return $this;
    }

    /**
     * Adiciona as informações de frete para o carrinho.
     *
     * @param $nome Nome do frete (mesmo nome utilizado nos serviços)
     * @param $prazoExtenso Prazo por extenso
     * @param $valor Valor formatado em decimal
     *
     * @param bool $save FALSE para não comitar as informações para o banco de dados.
     * @return $this
     */
    public function addFrete($nome, $prazoExtenso, $valor, $save = true)
    {
        $this->setFrete($nome);
        $this->setFretePrazo($prazoExtenso);
        $this->setValorEntrega($valor);
        if ($save) {
            $this->save();
            Config::getContainer()->getCarrinhoProvider()->restoreCart($this);
        }
        return $this;
    }

    /**
     * Verifica se todos os itens do carrinho estão em estoque.
     */
    public function checkStock()
    {
        /* @var $item PedidoItem */

        $inStock = true;
        $produtoTaxa = ProdutoPeer::retrieveByPK(ProdutoPeer::PRODUTO_TAXA_ID); /** @var Produto $produtoTaxa */
        $qtdCart = count($this->getPedidoItems());

        foreach ($this->getPedidoItems() as $item) {
            $produto = $item->getProdutoVariacao()->getProduto();

            if ($produtoTaxa->getId() == $produto->getId()) {
                continue;
            }

            if ($produto->isProdutoSimples()) {
                if ($item->getProdutoVariacao()->getSomaTotalEstoque() < 1) {
                    FlashMsg::add('info', 'O item ' . $item->getProdutoVariacao()->getProdutoNomeCompleto(' - ', '', ' - ') . ' teve seu estoque zerado.');

                    $item->setQuantidade(0);
                    $item->save();
                    //$item->delete();
                    $inStock = false;
                } elseif ($item->getProdutoVariacao()->getSomaTotalEstoque() < $item->getQuantidade()) {
                    FlashMsg::add('info', 'O item ' . $item->getProdutoVariacao()->getProdutoNomeCompleto(' - ', '', ' - ') .
                        ' teve seu estoque alterado para ' . $item->getProdutoVariacao()->getSomaTotalEstoque() . '.');

                    $item->setQuantidade($item->getProdutoVariacao()->getSomaTotalEstoque());
                    $item->save();
                    $inStock = false;
                } elseif (!$item->getProdutoVariacao()->isDisponivel()) {
                    FlashMsg::add('info', 'O item ' . $item->getProdutoVariacao()->getProdutoNomeCompleto(' - ', '', ' - ') .
                        ' não está disponível no momento.');

                    $item->setQuantidade(0);
                    $item->save();
                    //$item->delete();
                    $inStock = false;
                }
            } else {
                $arrProdutoCompostos = ProdutoCompostoQuery::create()->findByProdutoId($produto->getId());

                $menorQtdEstoqueComposto = null;

                $quantidade = $item->getQuantidade();



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
                    } elseif ($qtdEstoque < $qtdRequisitadaEstoque && $qtdEstoque <= 0) {
                        $menorQtdEstoqueComposto = 0;
                    }
                }

                if (is_numeric($menorQtdEstoqueComposto)) {
                    if ($menorQtdEstoqueComposto < 1) {
                        FlashMsg::add('info', 'O item ' . $item->getProdutoVariacao()->getProdutoNomeCompleto(' - ', '', ' - ') . ' teve seu estoque zerado.');

                        $item->setQuantidade(0);
                        $item->save();
                        //$item->delete();
                        $inStock = false;
                    } elseif ($menorQtdEstoqueComposto < $item->getQuantidade()) {
                        FlashMsg::add('info', 'O item ' . $item->getProdutoVariacao()->getProdutoNomeCompleto(' - ', '', ' - ') .
                            ' teve seu estoque alterado para ' . $menorQtdEstoqueComposto . '.');

                        $item->setQuantidade($menorQtdEstoqueComposto);
                        $item->save();
                        $inStock = false;
                    } elseif (!$item->getProdutoVariacao()->isDisponivel()) {
                        FlashMsg::add('info', 'O item ' . $item->getProdutoVariacao()->getProdutoNomeCompleto(' - ', '', ' - ') .
                            ' não está disponível no momento.');

                        $item->setQuantidade(0);
                        $item->save();
                        //$item->delete();
                        $inStock = false;
                    }
                } elseif (!$item->getProdutoVariacao()->isDisponivel()) {
                    FlashMsg::add('info', 'O item ' . $item->getProdutoVariacao()->getProdutoNomeCompleto(' - ', '', ' - ') .
                        ' não está disponível no momento.');

                    $item->setQuantidade(0);
                    $item->save();
                    //$item->delete();
                    $inStock = false;
                }
            }
        }

        if ($inStock == false) {
            $this->resetFrete();
            $this->unregisterCupom();
            if ($this->countPedidoFormaPagamentos()) {
                $this->getPedidoFormaPagamentos()->delete();
            }
        }

        return $inStock;
    }

    public function checkMinValueForSale()
    {

        if ($this->getCliente()->isPessoaJuridica()) {
            $minValue = Config::get('clientes.valor_minimo_pj');
        } elseif ($this->getCliente()->isPessoaFisica()) {
            $minValue = Config::get('clientes.valor_minimo_pf');
        } else {
            throw new Exception('Não foi possível determinar o tipo de pessoa deste carrinho.');
        }
        if ($this->getValorItens() <= $minValue) {
            FlashMsg::add('warning', '<b>Atenção!</b> Para finalizar sua compra, o valor do subtotal deve ser ' .
                'maior ou igual a R$ ' . format_money($minValue) . ' em itens.');
            return false;
        }

        return true;
    }

    /**
     * Atualiza os itens do carrinho com base em uma tabela de preço.
     *
     * @param null $tabelaPrecoId
     * @throws Exception
     * @throws PropelException
     */
    public function updatePedidoItemsByTabelaPrecoId($tabelaPrecoId = null)
    {

        /**
         * Verifica se o carrinho possui itens e atualiza os valores dos itens com base
         * na tabela fornecida por parâmetro.
         * Caso a tabela ou o produto associado à tabela não exista, o sistema atualiza com
         * o valor base do produto.
         */
        if ($this->countItems()) {
            $this->resetFrete();
            $this->unregisterCupom();

            $itens = $this->getPedidoItems();

            /* @var $objPedidoItem PedidoItem */
            foreach ($itens as $objPedidoItem) {
                $valorTabelado = TabelaPrecoVariacaoQuery::create()
                    ->filterByTabelaPrecoId($tabelaPrecoId)
                    ->filterByProdutoVariacaoId($objPedidoItem->getProdutoVariacaoId())
                    ->findOne();

                if ($valorTabelado instanceof TabelaPrecoVariacao) {
                    $valor = $valorTabelado->getValor();
                } else {
                    $valor = $objPedidoItem->getProdutoVariacao()->getValor();
                }

                $objPedidoItem->setValorUnitario($valor);
                $objPedidoItem->save();
            }

            FlashMsg::info('<b>Atenção!</b> Os valores dos produtos em seu carrinho podem ter ' .
                'sofrido alterações devido às configurações de seu cadastro!<br>' .
                'Em caso de dúvidas, entre em contato conosco.');
        }
    }

    public function checkQuantityPerItems()
    {

        $quantidadeRemovida = 0;
        foreach ($this->getPedidoItems() as $item) {
            if ($item->getQuantidade() < 1) {
                $item->delete();
                $quantidadeRemovida++;
            }
        }

        if ($quantidadeRemovida > 0) {
            FlashMsg::info(plural(
                $quantidadeRemovida,
                'Atenção: %s item foi removido por possuir quantidade igual a zero.',
                'Atenção: %s itens foram removidos por possuírem quantidade iguais a zero.'
            ));
        }
    }

    /**
     * Validação pois o não pode conter cliente revendedor sem plano.
     *
     * @return int
     * @throws PropelException
     */
    public function checkTypeClientAndCombo()
    {

        $clienteLogado = ClientePeer::getClienteLogado(true);
        $planoCliente =  $clienteLogado ? $clienteLogado->getPlano() : null;
        $tipoCliente = $clienteLogado ? $clienteLogado->getTipoConsumidor() : 0;
        $quantidadeRemovida = 0;

        if (!$planoCliente && $tipoCliente > 0) {
            foreach ($this->getPedidoItems() as $item) {
                $produto = $item->getProdutoVariacao()->getProduto();
                if ($produto && !$produto->isKitAdesao()) {
                    $item->delete();
                    $quantidadeRemovida++;
                }
            }
        }

        return $quantidadeRemovida;
    }

    /**
     * Verifica se é necessário solicitar um código de patrocinador ao cliente.
     * Um código de patrocinador é necessário caso o carrinho possua um kit de adesão e cliente logado ainda não possua um patrocinador.
     *
     * @return boolean
     * @throws PropelException
     */
    public function precisaPatrocinador()
    {
        return ClientePeer::isAuthenticad() && ($clienteLogado = ClientePeer::getClienteLogado(true)) && (!$clienteLogado->isInTree()) && $this->getPlano();
    }
    
    /**
     * Associa o patrocinador direto escolhido ao confirmar o pagamento do pedido.
     */
    public function associaPatrocinadorNaConfirmacaoPagamento()
    {
        global $container;
        
        if (!$this->precisaPatrocinador()) :
            throw new LogicException('Este pedido não precisa de um patrocinador.');
        endif;
        
        $objPatrocinador = null;
                
        //verifica se o cliente informou seu patrocinador
        if ($container->getSession()->get('PATROCINADOR_HOTSITE_ID')) { /* primeiro verifica se temos um patrocinador de hotsite na sessão. Se houver, usa ele como patrocinador */
            $objPatrocinador = ClienteQuery::create()->findPk($container->getSession()->get('PATROCINADOR_HOTSITE_ID'));
        } elseif ($container->getSession()->get('PATROCINADOR_CONFIRMADO') && $container->getSession()->get('PATROCINADOR_ID')) { /* senão usa o digitado pelo cliente no formulario */
            $objPatrocinador = ClienteQuery::create()->findPk($container->getSession()->get('PATROCINADOR_ID'));
        }

        $clienteLogado = ClientePeer::getClienteLogado(true);
        // seta o patrocinador (se houver) no cliente
        if ($objPatrocinador) :
            $clienteLogado->setClienteIndicadorId($objPatrocinador->getId());
            $clienteLogado->setClienteIndicadorDiretoId($objPatrocinador->getId());
            $clienteLogado->save();
            ClientePeer::setClienteLogado($clienteLogado);
        endif;

        // TODO: update this in future to use constructor
        $logger = new Logger('debug-channel');
        $logger->pushHandler(new StreamHandler('debug_app.log', Logger::DEBUG));
        $gerenciador = new GerenciadorRede(Propel::getConnection(), $logger);
        $query = ClienteQuery::create()->find();
        $count = count($query);

        if ($count == 1) :
            $gerenciador->insereRoot($clienteLogado);
        else :
            $gerenciador->insereRede($clienteLogado, $objPatrocinador);
        endif;

        $container->getSession()->remove('PATROCINADOR_CONFIRMADO');
        $container->getSession()->remove('CODIGO_PATROCINADOR');
    }

    /**
     * @throws PropelException
     */
    public function checkPlanForCustomer()
    {

        $quantidadeRemovida = 0;
        $planoCliente = ClientePeer::getClienteLogado(true)->getPlano();

        $valorPlanoCliente = 0;

        if ($planoCliente instanceof Plano) {
            $valorPlanoCliente = $planoCliente->getValor() > 0 ? $planoCliente->getValor() : ProdutoQuery::create()->findOneByPlanoId($planoCliente->getId())->getValor();
        }

        if ($planoCliente instanceof Plano) {
            foreach ($this->getPedidoItems() as $item) {
                if ($item->getProdutoVariacao()->getProduto()->isKitAdesao()) {
                    $produtoPlano = $item->getProdutoVariacao()->getProduto()->getPlano();

                    $valorPlanoAdicionar = $produtoPlano->getValor() > 0 ? $produtoPlano->getValor() : ProdutoQuery::create()->findOneByPlanoId($produtoPlano->getId())->getValor();

                    if ($valorPlanoCliente >= $valorPlanoAdicionar) {
                        $item->delete();
                        $quantidadeRemovida++;
                    }
                }
            }

            if ($quantidadeRemovida > 0) {
                FlashMsg::info(
                    'Atenção: Você não pode adquirir um plano menor ou igual que o atual ativo em seu cadastro.'
                );
            }
        }
    }

    public function elegivelFreteGratis()
    {

        if ($this->getTotalPontosProdutos() >= Config::get('pontos_minimos_frete_gratis')) :
            return true;
        endif;

        // // Promoção temporária para clientes finais
        // if ($this->getCliente()->isClienteFinal() && $this->getValorTotal() >= 199 && !$this->isKitAdesaoPedido(Propel::getConnection())) :
        //     return true;
        // endif;

        foreach ($this->getPedidoItems() as $item) :
            if ($item->getProdutoVariacao()->getProduto()->getFreteGratis()) :
                return true;
            endif;
        endforeach;

        return false;
    }

    public function getMaiorParcelaIndividual()
    {
        $maior = 0;

        foreach ($this->getPedidoItems() as $item) :
            if ($item->getProdutoVariacao()->getProduto()->getParcelamentoIndividual()) :
                $maior = max($maior, $item->getProdutoVariacao()->getProduto()->getParcelamentoIndividual());
            endif;
        endforeach;

        return $maior;
    }
}
