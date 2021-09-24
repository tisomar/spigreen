<?php

namespace QPress\Gateway\Services\SuperPayRest;

use QPress\Gateway\AbstractGateway;

use \Config as Config;

class SuperPayRest extends AbstractGateway
{

    const SANDBOX_URL = "https://sandbox.gateway.yapay.com.br/checkout/api/v3/transacao";
    const PRODUCTION_URL = "https://gateway.yapay.com.br/checkout/api/v3/transacao";

    public $request_url_append = '';

    /**
     * @var Request
     */
    private $request;

    const responseCodeAPIv3 = array(
        "1" => "paga",
        "2" => "paga_nao_capturada",
        "3" => "negada",
        "5" => "aguardando_pagamento",
        "8" => "aguardando_pagamento",
        "9" => "falha_na_operadora",
        "13" => "negada",
        "14" => "negada",
        "15" => "aguardando_pagamento",
        "17" => "negada",
        "18" => "falha_na_operadora",
        "21" => "negada",
        "22" => "paga",
        "23" => "negada",
        "24" => "negada",
        "25" => "negada",
        "27" => "negada",
        "31" => "negada",
        "40" => "negada"
    );

    const responseMessageCodeAPIv3 = array(
        "1" => "Transação está autorizada e confirmada na instituição financeira",
        "2" => "Transação está apenas autorizada, aguardando confirmação (captura)",
        "3" => "Transação negada pela instituição financeira",
        "5" => "Comum para pagamentos cartão redirect ou pagamentos com autenticação",
        "8" => "Comum para pagamentos com boletos e pedidos em reprocessamento",
        "9" => "Houve um problema no processamento com a adquirente",
        "13" => "Transação cancelada na adquirente",
        "14" => "A venda foi estornada na adquirente",
        "15" => "A transação foi enviada para o sistema de análise de riscos. Status transitório",
        "17" => "A transação foi negada pelo sistema análise de risco",
        "18" => "Falha. Não foi possível enviar pedido para a análise de Risco, porém será reenviado",
        "21" => "O boleto foi pago com valor menor do emitido",
        "22" => "O boleto foi pago com valor maior do emitido",
        "23" => "A venda estonada na adquirente parcialmente",
        "24" => "O Estorno não foi autorizado pela adquirente",
        "25" => "Falha ao enviar estorno para a operadora",
        "27" => "Pedido parcialmente cancelado na adquirente",
        "31" => "Transação já existente e finalizada na adquirente",
        "40" => "Processo de cancelamento em andamento"
    );

    /**
     *
     * @param String $establishment
     * @param String $env { sandbox | production }
     */
    function __construct($establishment, $env = 'sandbox', array $parameters = array())
    {
        $this->request = new Request();
        $this->request->token = $establishment;
        $this->request->url = ($env == "sandbox") ? self::SANDBOX_URL : self::PRODUCTION_URL;

        parent::__construct($parameters);
    }

    public function getName()
    {
        return 'SuperPay - REST';
    }

    public function getDefaultParameters()
    {
        return array(
            'pedido.moeda' => 'real',
            'pagamento.boleto' => Config::get('boleto.banco'),
            'pagamento.operadora' => Config::get('operadora_cartao_credito'),
            'pagamento.tipo_operacao' => Config::get('tipo_operacao'),
            'capturar' => Config::get('captura_automatica'),
            'url_retorno' => '',
        );
    }

    public function getData(\BasePedido $carrinho)
    {
        /**
         * Dados de pagamento
         */
        switch ($carrinho->getPedidoFormaPagamento()->getFormaPagamento()) {
            case \PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO:

                $data['pagamento']['meio_pagamento'] = $this->getParameter('pagamento.boleto');
                $data['pagamento']['data_vencimento'] = date('dmY', strtotime(sprintf("+%s days", Config::get('boleto.quantidade_dias_vencimento'))));

                $data['capturar'] = $this->getParameter('capturar');
                $data['url_retorno'] = $this->getParameter('url_retorno');

                /**
                 * Dados do pedido
                 */
                $data['pedido']['numero'] = $carrinho->getId();
                $data['pedido']['total'] = $carrinho->getValorTotal();
                $data['pedido']['moeda'] = $this->getParameter('pedido.moeda');

                /**
                 * Dados do comprador
                 */
                if ($carrinho->getCliente()->isPessoaFisica()) {
                    $data['comprador']['nome'] = $carrinho->getCliente()->getNomeCompleto();
                    $data['comprador']['documento'] = preg_replace('/[^0-9]/', '', $carrinho->getCliente()->getCpf());
                } else {
                    $data['comprador']['nome'] = $carrinho->getCliente()->getRazaoSocial();
                    $data['comprador']['documento'] = preg_replace('/[^0-9]/', '', $carrinho->getCliente()->getCnpj());
                }
                $data['comprador']['endereco'] = $carrinho->getEndereco()->getLogradouro();
                $data['comprador']['numero'] = $carrinho->getEndereco()->getNumero();
                $data['comprador']['cep'] = $carrinho->getEndereco()->getCep();
                $data['comprador']['bairro'] = $carrinho->getEndereco()->getBairro();
                $data['comprador']['cidade'] = $carrinho->getEndereco()->getCidade()->getNome();
                $data['comprador']['estado'] = $carrinho->getEndereco()->getCidade()->getEstado()->getSigla();
                break;

            case \PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CARTAO_CREDITO:

                $post = $this->getDefaultHttpRequest();
                $post_cartao = $post->request->get('cartao');

                $bandeira = strtolower($carrinho->getPedidoFormaPagamento()->getBandeira());
                $cartao_numero = preg_replace('/[^0-9]/', '', $post_cartao['numero']);
                $cartao_cvv = $post_cartao['codigo_seguranca'];
                $cartao_validade = str_pad($post_cartao['validade_mes'], 2, '0', STR_PAD_LEFT) . '/' . $post_cartao['validade_ano'];
                $cartao_titular = $post_cartao['titular'];

                $data['codigoEstabelecimento'] = $this->request->token;
                $data['codigoFormaPagamento'] = \PedidoFormaPagamentoPeer::CODIGOS_FORMA_PAGAMENTO[strtolower($carrinho->getPedidoFormaPagamento()->getBandeira())];

                $data['transacao']['numeroTransacao'] = $carrinho->getId();
                $data['transacao']['valor'] = $carrinho->getValorTotal() * 100;
                $data['transacao']['moeda'] = $this->getParameter('pedido.moeda');
                $data['transacao']['parcelas'] = $carrinho->getPedidoFormaPagamento()->getNumeroParcelas();

                $data['dadosCartao']['nomePortador'] = $cartao_titular;
                $data['dadosCartao']['numeroCartao'] = $cartao_numero;
                $data['dadosCartao']['codigoSeguranca'] = $cartao_cvv;
                $data['dadosCartao']['dataValidade'] = $cartao_validade;

                $data['dadosCobranca']['nome'] = $carrinho->getCliente()->getNomeCompleto();
                $data['dadosCobranca']['documento'] = $carrinho->getCliente()->isPessoaFisica() ? $carrinho->getCliente()->getCpf() : $carrinho->getCliente()->getCnpj();


                $items = $carrinho->getPedidoItems();
                $arrItems = array();
                foreach ($items as $item) {
                    $arrItems[] = array(
                        "quantidadeProduto" => $item->getQuantidade(),
                        "valorUnitarioProduto" => $item->getValorUnitario());
                }

                $data['itensDoPedido'] = $arrItems;


                break;

            default:
                break;
        }

        return $data;
    }

    public function purchase(\BasePedidoFormaPagamento $formaPagamento)
    {
        $carrinho = $formaPagamento->getPedido();
        $submitData = $this->getData($carrinho);

        $headers = array(
            'Content-Type: application/json; charset=utf-8',
            'Content-length: ' . strlen(json_encode($submitData))
        );
        $username = \Config::get('superpay.username_production');
        $pass = \Config::get('superpay.password_production');

        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $this->request->url);
        curl_setopt($curl_handle, CURLOPT_POST, TRUE);
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, json_encode($submitData));
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl_handle, CURLOPT_HEADER, FALSE);
        curl_setopt($curl_handle, CURLOPT_USERPWD, "$username:$pass");
        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl_handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($curl_handle);

        if (curl_error($curl_handle)) {
            $error_msg = curl_error($curl_handle);
        }
        $responseaArray = json_decode($response, true);

        $replay = array(
            "pedido_id" => $responseaArray['numeroTransacao'],
            'id' => $responseaArray['numeroComprovanteVenda'],
            'tid' => $responseaArray['numeroComprovanteVenda'],
            "status" => self::responseCodeAPIv3[$responseaArray['statusTransacao']] ,
            "erro" => self::responseCodeAPIv3[$responseaArray['statusTransacao']] != "paga" ? $responseaArray['statusTransacao'] : false,
            'code' => $responseaArray['statusTransacao'],
            'message' => self::responseMessageCodeAPIv3[$responseaArray['statusTransacao']],
            "url_acesso" => $responseaArray['urlPagamento'],
        );
        $data = json_decode(json_encode($replay));
        $obj = new Response\Response($data);
        return $obj;
    }

    public function consult($id)
    {
        $this->request->httpMethod = 'get';
        $this->request_url_append .= '/' . $id;
        return $this->sendRequest();
    }

    public function sendRequest($payment = null)
    {
        $this->request->url .= $this->request_url_append;
        if ($payment) {
            $this->request->payload = $payment->toPayload();
        }
        return $this->request->execute();
    }

    // ------------------------------------------------------------------------
//    static public function capturar($id = null)
//    {
//        $gateway = new self;
//        $gateway->request_url_append .= '/' . $id . '/capturar';
//        return $gateway;
//    }
//
//    static public function cancelar($id = null)
//    {
//        $gateway = new self;
//        $gateway->request_url_append .= '/' . $id . '/estornar';
//        return $gateway;
//    }
}
