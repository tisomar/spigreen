<?php

namespace QPress\Carrinho\Provider;

use PedidoFormaPagamentoPeer;
use PedidoFormaPagamentoQuery;
use QPress\Carrinho\Storage\CarrinhoStorage;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use QPress\Frete\Manager\FreteManager;

class CarrinhoProvider {

    /**
     * @var CarrinhoStorage $session
     */
    public $session;

    /**
     * @var \CarrinhoQuery $repository
     */
    protected $repository;

    /**
     * @var \Carrinho $carrinho
     */
    protected $carrinho;

    /**
     *
     * @var \QPress\Frete\Manager\FreteManager $frete_manager
     */
    protected $frete_manager;

    /**
     * @param SessionInterface $session
     * @param FreteManager $frete_manager
     */
    function __construct(SessionInterface $session, FreteManager $frete_manager) {
        $this->session = new CarrinhoStorage($session);
        $this->repository = new \CarrinhoQuery();
        $this->frete_manager = $frete_manager;

        $this->getCarrinho();
    }

    /**
     * Retorna o carrinho atual. Caso ele não exista, este método fará com que o sistema
     * crie um para retornar.
     *
     * @return \Carrinho
     */
    public function getCarrinho($createIfNotExists = false) {

        $carrinhoId = $this->session->getCurrentCarrinhoId();

        if ($carrinhoId && $carrinho = $this->getCarrinhoById($carrinhoId)) {
            $this->carrinho = $carrinho;
        } else {
            $this->carrinho = $this->repository->createNew();
            $this->session->setCurrentCarrinhoId($this->carrinho);
        }
        $this->carrinho->setFreteManager($this->frete_manager);

        if ($createIfNotExists && $this->carrinho->isNew()) {
            if (\ClientePeer::isAuthenticad()) {
                $this->carrinho->setClienteId(\ClientePeer::getClienteLogado()->getId());
            }
            $this->carrinho->save();
        }

        return $this->carrinho;
    }

    /**
     * Retorna o carrinho atualizado.
     *
     * @param integer $id
     * @return \Carrinho
     */
    public function getCarrinhoById($id) {
        return $this->repository->filterByClassKey(2)->findPk($id);
    }

    public function finalizarCarrinho($carrinho = null)
    {
        if ($carrinho == null) {
            $carrinho = $this->getCarrinho();
        }

        // Altera a class_key para 1 - Pedido
        $carrinho->setClassKey(1);

        // Atualiza a data de criação do pedido para quando o cliente finalizou a compra
        $carrinho->setCreatedAt(new \DateTime('now'));
        $carrinho->setUpdatedAt(new \DateTime('now'));
        
        // Salva o valor ajustado do cupom de desconto para este pedido.
        $carrinho->setValorCupomDesconto($carrinho->getValorDesconto(\CupomPeer::getOMClass()));

        // Salva as informações no banco
        $carrinho->save();

        foreach ($carrinho->getPedidoItems() as $item) :
            $variacao = $item->getProdutoVariacao();

            if ($variacao->getProdutoId() == 149):
                foreach ($carrinho->getPedidoItemsAll($variacao->getProduto()->getPlanoId()) as $pedidoItem):
                    $variacao = $pedidoItem->getProdutoVariacao();

                    $variacao->diminuirEstoque($pedidoItem->getQuantidade(), $carrinho->getId());
                endforeach;
            else:
                $variacao->diminuirEstoque($item->getQuantidade(), $carrinho->getId());
            endif;
            
            // if (isset($taxaCadastro) && $taxaCadastro == 'taxa_cadastro') :
            //     $carrinho->setTaxaCadastro(1);
            //     $carrinho->save();
            // endif;
        endforeach;

        $objPedidoStatusHistorico = $carrinho->avancaStatus();

        /**
         * Avança o status para pagamento confirmado quando:
         *  - A configuração de captura automática for "Sim",
         *  - Quando o Status do Pedido for "Aguardando Pagamento", e
         *  - Quando o Status do Pagamento for Aprovado.
         */

        if (\Config::get('captura_automatica') == "true") :
            if ($objPedidoStatusHistorico->getPedidoStatusId() == \PedidoStatusPeer::STATUS_ID_AGUARDANDO_PAGAMENTO) :
                $hasPaymentPending = PedidoFormaPagamentoQuery::create()
                    ->filterByPedidoId($carrinho->getId())
                    ->filterByStatus(PedidoFormaPagamentoPeer::STATUS_PENDENTE)
                    ->count() > 0;

                if (!$hasPaymentPending) :
                    sleep(1); // Adicionado para não permitir um novo status no mesmo segundo.
                    $carrinho->avancaStatus();
                endif;
            endif;
        endif;

        $cliente = $carrinho->getCliente();

        if (empty($cliente->getPlanoId())) :
            $carrinho->setHotsiteClienteId($cliente->getClienteIndicadorId());
            $carrinho->save();
        endif;

        /**
         * Envia um e-mail com a compra para o cliente
         */
        \QPress\Mailing\Mailing::pedidoNovo($carrinho);

        $this->carrinho = null;

        // Remove o carrinho atual da sessão
        $this->session->resetCurrentCarrinhoId();
    }

    public function save() {
        $cart = $this->getCarrinho();
        $cart->save();
        $this->session->setCurrentCarrinhoId($cart);
    }

    /**
     * Restaura um carrinho para a sessão se ele for abandonado
     * @param $id
     */
    public function restoreCart(\Carrinho $cart) {
        if ($this->isAbandonedCart($cart)) {
            $this->session->setCurrentCarrinhoId($cart);
            $this->getCarrinho();
        }
    }

    /**
     * Verifica se o carrinho está abandonado.
     * @param $id
     * @return bool
     */
    public function isAbandonedCart($cart) {
        return \PedidoQuery::create()
            ->filterByStatus(\PedidoPeer::STATUS_ANDAMENTO)
            ->filterByClassKey(\PedidoPeer::CLASS_KEY_CARRINHO)
            ->filterById($cart->getId())
            ->count() == 1;
    }

}
