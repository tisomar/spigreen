<?php

namespace QPress\Gateway\Services\PayPal;

/**
 * Description of PayPal
 *
 * @author Garlini
 */
class PayPal extends \QPress\Gateway\AbstractGateway
{
    const AMBIENTE_SANDBOX = 'sandbox';
    const AMBIENTE_PRODUCTION = 'production';
    
    const API_VERSION = '108.0';
    
    protected $ambiente;
    
    protected $username;
    
    protected $password;
    
    protected $signature;

    /**
     * 
     * @param string $ambiente
     * @param string $username
     * @param string $password
     * @param string $signature
     */
    public function __construct($ambiente, $username, $password, $signature)
    {
        parent::__construct();
        
        if (!in_array($ambiente, array(self::AMBIENTE_SANDBOX, self::AMBIENTE_PRODUCTION), true)) {
            throw new \InvalidArgumentException('Ambiente invalido.');
        }
        
        $this->ambiente = $ambiente;
        $this->username = $username;
        $this->password = $password;
        $this->signature = $signature;
    }

    public function getDefaultParameters()
    {
        return array();
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'PayPal';
    }

    /**
     * Inicia o processo de pagamento.
     * 
     * @param \BasePedido $pedido
     * @return \QPress\Gateway\Services\PayPal\Response\Response
     */
    public function purchase(\BasePedidoFormaPagamento $formaPagamento)
    {
        $pedido = $formaPagamento->getPedido();
        //Primeiro precisamos chamar o metodo "SetExpressCheckout" usando a API do PayPal.
        $requestNvp = array(
            'USER' => $this->username,
            'PWD' => $this->password,
            'SIGNATURE' => $this->signature,
            'VERSION' => self::API_VERSION,
            'METHOD' => 'SetExpressCheckout',
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'SALE',
            'PAYMENTREQUEST_0_AMT' => $this->formataValor($pedido->getValorTotal()),
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'BRL',
            'PAYMENTREQUEST_0_ITEMAMT' => $this->formataValor($pedido->getValorItens()),
            'PAYMENTREQUEST_0_SHIPPINGAMT' => $this->formataValor($pedido->getValorEntrega()),
            'PAYMENTREQUEST_0_INVNUM' => $pedido->getId(),
            'RETURNURL' => $this->getUrlRetornoSucesso(),
            'CANCELURL' => $this->getUrlRetornoCancelamento(),
            'BUTTONSOURCE' => 'BR_EC_EMPRESA'
        );
        
        $indiceItem = 0;
        foreach ($pedido->getPedidoItems() as $item) {
            
            $produto = $item->getProdutoVariacao()->getProduto();
            
            $requestNvp["L_PAYMENTREQUEST_0_NAME$indiceItem"] = $this->formataTexto($produto->getNome(), 127);
            $requestNvp["L_PAYMENTREQUEST_0_DESC$indiceItem"] = $this->formataTexto($produto->getDescricao(), 127);
            $requestNvp["L_PAYMENTREQUEST_0_AMT$indiceItem"] = $this->formataValor($item->getValorUnitario());
            $requestNvp["L_PAYMENTREQUEST_0_QTY$indiceItem"] = $item->getQuantidade();
            
            $indiceItem += 1;
        }
                
        $responseNvp = $this->sendNvpRequest($requestNvp);
                                
        //Verifica se retornou sucesso.
        if (isset($responseNvp['ACK']) && strtolower($responseNvp['ACK']) === 'success' && isset($responseNvp['TOKEN'])) {
            
            $paypalURL = 'https://www.' . ($this->isAmbienteSandbox() ? 'sandbox.' : '') . 'paypal.com/cgi-bin/webscr';
            
            $query = array(
                'cmd'	=> '_express-checkout',
                'token'	=> $responseNvp['TOKEN'],
                'useraction' => 'commit'
            );
            
            //Agora precisamos redirecionar o usuario para a pagina de pagamento no PayPal.
            //Retorna uma resposta de redirect.
            $response = new Response\Response(array(
                'successful' => true, //indica sucesso
                'url' => $paypalURL . '?' . http_build_query( $query )
            ));
            
            return $response;
            
        } 
        
        //Vamos retornar uma resposta de erro.
        $strMessage = $this->getErrorMessage($responseNvp);
        $response = new Response\Response(array(
            'successful' => false,
            'message' => $strMessage ? $strMessage : 'Falha na comunicação com o PayPal.' //adiciona uma mensagem genérica se não encontrar uma melhor
        ));

        return $response;
    }
    
    /**
     * Consulta os dados do checkout.
     * 
     * @param string $token
     * @return array
     */
    public function consultCheckout($token)
    {
        $nvp = array(
            'TOKEN' => $token,
            'METHOD' => 'GetExpressCheckoutDetails',
            'VERSION' => self::API_VERSION,
            'PWD' => $this->password,
            'USER' => $this->username,
            'SIGNATURE' => $this->signature
        );
        
        $responseNvp = $this->sendNvpRequest($nvp);
        
        return $responseNvp;
    }
    
    /**
     * É esta função que finaliza o pagamento no PayPal. Só após a chamada do método do PayPal "DoExpressCheckoutPayment" que o pagamento estará concluido.
     * 
     * @param array $responseNvp Dados retornados na consulta ao metodo do PayPal "GetExpressCheckoutDetails"
     * @return array
     */
    public function checkoutPayment($responseNvp)
    {
        //verifica campos obrigatorios
        foreach (array('TOKEN', 'PAYERID', 'PAYMENTREQUEST_0_AMT') as $chave) {
            if (!isset($responseNvp[$chave])) {
                throw new \LogicException("A chave \"$chave\" é obrigatória.");
            }
        }
        
        $nvp = array(
            'TOKEN' => $responseNvp['TOKEN'],
            'METHOD' => 'DoExpressCheckoutPayment',
            'VERSION' => self::API_VERSION,
            'PWD' => $this->password,
            'USER' => $this->username,
            'SIGNATURE' => $this->signature,
            'PAYERID' => $responseNvp['PAYERID'],
            'PAYMENTREQUEST_0_AMT' => $responseNvp['PAYMENTREQUEST_0_AMT'],
            'PAYMENTREQUEST_0_CURRENCYCODE' => 'BRL'
        );
        
        $responseNvp = $this->sendNvpRequest($nvp);
        
        return $responseNvp;
    }
    
    /**
     * Consulta os dados de uma transação.
     *  
     * @param string $transactionid Id da transação (retornado pelo metodo do Paypal "DoExpressCheckoutPayment").
     * @return array
     */
    public function consultTransaction($transactionid)
    {
        $nvp = array(
            'TRANSACTIONID' => $transactionid,
            'METHOD' => 'GetTransactionDetails',
            'VERSION' => self::API_VERSION,
            'PWD' => $this->password,
            'USER' => $this->username,
            'SIGNATURE' => $this->signature
        );
        
        $responseNvp = $this->sendNvpRequest($nvp);
        
        return $responseNvp;
    }

    /**
     * 
     * @param array $requestNvp
     * @param int $tentativa
     * @return array
     */
    protected function sendNvpRequest(array $requestNvp, $tentativa = 1)
    {
        $apiEndpoint = 'https://api-3t.' . ($this->isAmbienteSandbox() ? 'sandbox.' : '');
        $apiEndpoint .= 'paypal.com/nvp';

        $curl = curl_init();
        if (false === $curl) {
            throw new \RuntimeException('Falha ao iniciar o curl.');
        }

        curl_setopt($curl, CURLOPT_URL, $apiEndpoint);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($requestNvp));

        $response = urldecode(curl_exec($curl));

        curl_close($curl);

        $responseNvp = array();

        if (preg_match_all('/(?<name>[^\=]+)\=(?<value>[^&]+)&?/', $response, $matches)) {
            foreach ($matches['name'] as $offset => $name) {
                $responseNvp[$name] = $matches['value'][$offset];
            }
        }
                
        if (isset($responseNvp['L_ERRORCODE0']) && '10001' === $responseNvp['L_ERRORCODE0']) {
            //Este é o codigo de erro interno do servidor do PayPal. A documentação diz que podemos tentar novamente quando este erro acontecer.
            if ($tentativa < 5) {
                sleep(10);
                //tenta novamente
                return $this->sendNvpRequest($requestNvp, $tentativa + 1);
            }
        }

        if (isset($responseNvp['ACK']) && strtolower($responseNvp['ACK']) !== 'success') {
            for ($i = 0; isset($responseNvp['L_ERRORCODE' . $i]); ++$i) {
                $message = sprintf("PayPal NVP %s[%d]: %s\n", $responseNvp['L_SEVERITYCODE' . $i], $responseNvp['L_ERRORCODE' . $i], $responseNvp['L_LONGMESSAGE' . $i]);

                error_log($message);
            }
        }

        return $responseNvp;
    }
    
    /**
     * 
     * @param float $valor
     * @return string
     */
    protected function formataValor($valor)
    {
        return number_format($valor, 2, '.', '');
    }
    
    /**
     * 
     * @param string $texto
     * @param int $length
     * @return string
     */
    protected function formataTexto($texto, $length)
    {
        return substr(strip_tags(utf8_decode($texto)), 0, $length);
    }

    /**
     * 
     * @return bool
     */
    public function isAmbienteSandbox()
    {
        return self::AMBIENTE_SANDBOX === $this->ambiente;
    }
        
    /**
     * 
     * @return string
     */
    protected function getUrlRetornoSucesso()
    {
        return get_url_site() . '/carrinho/retorno-paypal/';
    }
    
    /**
     * 
     * @return string
     */
    protected function getUrlRetornoCancelamento()
    {
        return get_url_site() . '/carrinho/cancelamento-paypal/';
    }
    
    
    /**
     * Este função pega o retorno do PayPal e tenta encontrar uma mensagem adequada para exibir ao usuário no frontend. 
     * Quando o erro for desconhecido a função retorna uma string vazia.
     * 
     * @param array $responseNvp
     * @return string
     */
    protected function getErrorMessage($responseNvp)
    {
        if (isset($responseNvp['L_LONGMESSAGE0'])) {
            $longMessage = $responseNvp['L_LONGMESSAGE0'];
            switch (true) {
                case 'Security header is not valid' === $longMessage:
                    return 'Credenciais inválidas.';
            }
        }
        
        if (isset($responseNvp['L_ERRORCODE0'])) {
            $errorCode = $responseNvp['L_ERRORCODE0'];
            switch (true) {
                case '10001' === $errorCode:
                    return 'Erro interno no servidor do PayPal.';
            }
        }
        
        return '';
    }
    
}
