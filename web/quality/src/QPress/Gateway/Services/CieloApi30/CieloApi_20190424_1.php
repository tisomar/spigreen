<?php

namespace QPress\Gateway\Services\CieloApi30;

use QPress\Gateway\AbstractGateway;
use QPress\Gateway\Helper\Helper;
use QPress\Gateway\Services\CieloApi30\Credito\CartaoCreditoCielo;
use QPress\Gateway\Services\CieloApi30\Debito\CartaoDebitoCielo;
use QPress\Gateway\Services\CieloApi30\Recorrencia\CartaoRecorrenciaCielo;
use QPress\Gateway\Services\CieloApi30\Boleto\BoletoCieloBB;

class CieloApi extends AbstractGateway
{

    private $processor;
    private $tipoPagamento;
    private $ambiente;
    private $acesso;



    public function __construct($ambiente = 'sandbox')
    {
        $creditCard = new CartaoCreditoCielo();
        $debitCard = new CartaoDebitoCielo();
        $recorrencePayment = new CartaoRecorrenciaCielo();
        $boletoBB = new BoletoCieloBB();
        $this->processor = array(
            'credito' => $creditCard,
            'debito' => $debitCard,
            'recorrencia' => $recorrencePayment,
            'boleto_bb' => $boletoBB
        );


        $this->ambiente = array(
            'ambiente' => $ambiente,
            'merchant_id' => \Config::get('cielo.merchant_id_'.$ambiente),
            'merchant_key' => \Config::get('cielo.merchant_key_'.$ambiente)
        );

    }

    public function getDefaultParameters()
    {

    }

    public function getName()
    {
        return 'Cielo Api 3.0';
    }

    public function purchase(\BasePedidoFormaPagamento $formaPagamento)
    {

        $carrinho = $formaPagamento->getPedido();
        /** @var \Cliente $cliente */
        if($carrinho->getPedidoFormaPagamento()->getFormaPagamento() == \PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_DEBITO
            || $carrinho->getPedidoFormaPagamento()->getFormaPagamento() == \PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_CREDITO
        ) {

            $post = $this->getDefaultHttpRequest();
            $post_cartao = $post->request->get('cartao');

            $cieloDados = new \CartaoCieloDados();
            $cieloDados->setNome($post_cartao["titular"]);
            $cieloDados->setNumero($post_cartao["numero"]);
            $cieloDados->setValidadeMes($post_cartao["validade_mes"]);
            $cieloDados->setValidadeAno($post_cartao["validade_ano"]);
            $cieloDados->setCodigo($post_cartao["codigo_seguranca"]);
            $cieloDados->setPedidoId($carrinho->getId());
            $cieloDados->setTipo($carrinho->getPedidoFormaPagamento()->getFormaPagamento());
            $cieloDados->setBandeira($carrinho->getPedidoFormaPagamento()->getBandeira());
            $cieloDados->setCpf($carrinho->getCliente()->getCpf());

            if ($cieloDados->getTipo() == \PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_DEBITO) {
                /** @var CartaoDebitoCielo $objectPgto */
                $objectPgto = $this->processor['debito'];
            } elseif ($cieloDados->getTipo() == \PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_CREDITO) {
                /** @var CartaoCreditoCielo $objectPgto */
                $objectPgto = $this->processor['credito'];
            }
        } else {
            /** @var \BoletoCieloDados $cieloDados */
            $cieloDados = new \BoletoCieloDados();

            //var_dump(\Config::get('cielo-boleto.provider'));die;

            $cieloDados->setPedidoId($carrinho->getId());
            $cieloDados->setTipo($carrinho->getPedidoFormaPagamento()->getFormaPagamento());
            $cieloDados->setProvider(\Config::get('cielo-boleto.provider'));
            $cieloDados->setAssignor(\Config::get('cielo-boleto.assignor'));
            $cieloDados->setInstructions(\Config::get('cielo-boleto.instructions'));
            $cieloDados->setAddress(\Config::get('cielo-boleto.address'));
            $cieloDados->setQuantidadeDiasVencimento(\Config::get('cielo-boleto.quantidade_dias_vencimento'));
            $cieloDados->setExpirationDate($this->generateExpirationDate($cieloDados));


            if ($cieloDados->getTipo() == \PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_BOLETO_BB) {
                /** @var CartaoDebitoCielo $objectPgto */
                $objectPgto = $this->processor['boleto_bb'];
            } elseif ($cieloDados->getTipo() == \PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_BOLETO_BRADESCO) {
                /** @var CartaoCreditoCielo $objectPgto */
                $objectPgto = $this->processor['boleto_bradesco'];
            }

        }

        return $objectPgto->pagar($carrinho,  $this->ambiente, $cieloDados);


        //$submitData = $this->getData($carrinho);
        //return $this->processor->pagamentoCompleto($submitData);
    }
    
    public function consult() {
        
    }

    public function getData(\BasePedido $carrinho)
    {

    }

    public function resolveIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    public function autenticatePaymentOrderDebit(\BasePedido $carrinho, $cartaoCieloDados)
    {
        $objectPgto = $this->processor['debito'];
        return $objectPgto->autenticarPagamento($carrinho,  $this->ambiente, $cartaoCieloDados);
    }

    /**
     * @param $cieloDados
     * @return mixed
     */
    private function generateExpirationDate($cieloDados)
    {
        $dateExpiration = \DateTime::createFromFormat('Y-m-d',date('Y-m-d'));
        $dateExpiration->add(new \DateInterval('P'.$cieloDados->getQuantidadeDiasVencimento().'D'));
        return $dateExpiration->format('Y-m-d');
    }

}
