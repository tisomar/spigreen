<?php

namespace QPress\Gateway\Services\SuperPay;

use QPress\Gateway\AbstractGateway;
use QPress\Gateway\Helper\Helper;

class SuperPay extends AbstractGateway
{

    private $processor;

    public function __construct($processor, array $parameters = array())
    {
        $this->processor = $processor;
        parent::__construct($parameters);
    }

    public function getDefaultParameters()
    {
        return array(
            'estabelecimento' => '',
            'ip' => $this->resolveIP()
        );
    }

    public function getName()
    {
        return 'SuperPay - WS';
    }

    public function purchase(\BasePedidoFormaPagamento $formaPagamento)
    {
        $carrinho = $formaPagamento->getPedido();
        $submitData = $this->getData($carrinho);
        return $this->processor->pagamentoCompleto($submitData);
    }
    
    public function consult() {
        
    }

    public function getData(\BasePedido $carrinho)
    {
        // Dados da pessoa separados por tipo
        if ($carrinho->getCliente()->isPessoaJuridica())
        {
            $documentoComprador = $carrinho->getCliente()->getCnpj();
            $nomeComprador = $carrinho->getCliente()->getRazaoSocial();
            $tipoCliente = 2;
        }
        else
        {
            $documentoComprador = $carrinho->getCliente()->getCpf();
            $nomeComprador = $carrinho->getCliente()->getNomeCompleto();
            $tipoCliente = 1;
        }

        // Dados do telefone
        $telefone = explode_telefone($carrinho->getCliente()->getTelefone());

        /**
         * 
         * Dados da transação
         * 
         */
        $dados_envio["numeroTransacao"] = $carrinho->getId();
        $dados_envio["codigoEstabelecimento"] = '1387259152023';

        if ($carrinho->getPedidoFormaPagamento()->getFormaPagamento() == \PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CARTAO_CREDITO)
        {
            $dados_envio["nomeTitularCartaoCredito"] = "Manoel Moreira";
            $dados_envio["numeroCartaoCredito"] = 5555666677778884;
            $dados_envio["codigoSeguranca"] = 321;
            $dados_envio["dataValidadeCartao"] = "12/2015";
            $dados_envio["parcelas"] = $carrinho->getPedidoFormaPagamento()->getNumeroParcelas();
        }

        $dados_envio['campoLivre1'] = "";
        $dados_envio['campoLivre2'] = "";
        $dados_envio['campoLivre3'] = "";
        $dados_envio['campoLivre4'] = "";
        $dados_envio['campoLivre5'] = "";

        /**
         *  28 - Boleto: Banco do Brasil
         * 
         */
        $dados_envio["codigoFormaPagamento"] = 28;

        $dados_envio["valor"] = ($carrinho->getValorTotal() * 100); // R$ 10,05

        $dados_envio["dadosUsuarioTransacao"]["bairroEnderecoComprador"] = 
        $dados_envio["dadosUsuarioTransacao"]["bairroEnderecoEntrega"] = $carrinho->getEndereco()->getBairro();

        $dados_envio["dadosUsuarioTransacao"]["cepEnderecoComprador"] = 
        $dados_envio["dadosUsuarioTransacao"]["cepEnderecoEntrega"] = preg_match('/[^0-9]/', $carrinho->getEndereco()->getCep());

        $dados_envio["dadosUsuarioTransacao"]["cidadeEnderecoComprador"] = 
        $dados_envio["dadosUsuarioTransacao"]["cidadeEnderecoEntrega"] = $carrinho->getEndereco()->getCidade()->getNome();

        $dados_envio["dadosUsuarioTransacao"]["codigoCliente"] = $carrinho->getClienteId();

        $dados_envio["dadosUsuarioTransacao"]["codigoTipoTelefoneAdicionalComprador"] = 
        $dados_envio["dadosUsuarioTransacao"]["codigoTipoTelefoneAdicionalEntrega"] = null;

        $dados_envio["dadosUsuarioTransacao"]["codigoTipoTelefoneComprador"] =
        $dados_envio["dadosUsuarioTransacao"]["codigoTipoTelefoneEntrega"] = 1;

        $dados_envio["dadosUsuarioTransacao"]["complementoEnderecoComprador"] = 
        $dados_envio["dadosUsuarioTransacao"]["complementoEnderecoEntrega"] = $carrinho->getEndereco()->getComplemento();

        $dados_envio["dadosUsuarioTransacao"]["dataNascimentoComprador"] = $carrinho->getCliente()->getDataNascimento('d/m/Y');

        $dados_envio["dadosUsuarioTransacao"]["dddAdicionalComprador"] = 
        $dados_envio["dadosUsuarioTransacao"]["dddAdicionalEntrega"] = null;

        $dados_envio["dadosUsuarioTransacao"]["dddComprador"] = 
        $dados_envio["dadosUsuarioTransacao"]["dddEntrega"] = $telefone['ddd'];

        $dados_envio["dadosUsuarioTransacao"]["ddiAdicionalComprador"] = 
        $dados_envio["dadosUsuarioTransacao"]["ddiAdicionalEntrega"] = null;

        $dados_envio["dadosUsuarioTransacao"]["ddiComprador"] = $dados_envio["dadosUsuarioTransacao"]["ddiEntrega"] = 55;

        $dados_envio["dadosUsuarioTransacao"]["documentoComprador"] = $documentoComprador;
        $dados_envio["dadosUsuarioTransacao"]["documento2Comprador"] = null;
        $dados_envio["dadosUsuarioTransacao"]["emailComprador"] = $carrinho->getCliente()->getEmail();

        $dados_envio["dadosUsuarioTransacao"]["enderecoComprador"] = 
        $dados_envio["dadosUsuarioTransacao"]["enderecoEntrega"] = $carrinho->getEndereco()->getLogradouro();

        $dados_envio["dadosUsuarioTransacao"]["estadoEnderecoComprador"] = 
        $dados_envio["dadosUsuarioTransacao"]["estadoEnderecoEntrega"] = $carrinho->getEndereco()->getCidade()->getEstado()->getNome();

        $dados_envio["dadosUsuarioTransacao"]["nomeComprador"] = $nomeComprador;

        $dados_envio["dadosUsuarioTransacao"]["numeroEnderecoComprador"] = 
        $dados_envio["dadosUsuarioTransacao"]["numeroEnderecoEntrega"] = $carrinho->getEndereco()->getNumero();

        $dados_envio["dadosUsuarioTransacao"]["sexoComprador"] = "m";

        $dados_envio["dadosUsuarioTransacao"]["telefoneAdicionalComprador"] = 
        $dados_envio["dadosUsuarioTransacao"]["telefoneAdicionalEntrega"] = null;

        $dados_envio["dadosUsuarioTransacao"]["telefoneComprador"] = 
        $dados_envio["dadosUsuarioTransacao"]["telefoneEntrega"] = $telefone['telefone'];

        $dados_envio["dadosUsuarioTransacao"]["tipoCliente"] = $tipoCliente;

        $dados_envio["IP"] = $this->getParameter('ip');

        $dados_envio["idioma"] = "1";

        foreach ($carrinho->getPedidoItems() as $i => $item)
        {
            $dados_envio["itensDoPedido"][$i]["codigoProduto"] = $item->getProdutoVariacao()->getProdutoId();
            $dados_envio["itensDoPedido"][$i]["codigoCategoria"] = null;
            $dados_envio["itensDoPedido"][$i]["nomeProduto"] = $item->getProdutoVariacao()->getProduto()->getNome();
            $dados_envio["itensDoPedido"][$i]["quantidadeProduto"] = $item->getQuantidade();
            $dados_envio["itensDoPedido"][$i]["valorUnitarioProduto"] = $item->getValorUnitario();
            $dados_envio["itensDoPedido"][$i]["nomeCategoria"] = null;
        }

        $dados_envio["origemTransacao"] = "1";

        $dados_envio["taxaEmbarque"] = "0";

        $dados_envio["urlCampainha"] = "";

        $dados_envio["urlRedirecionamentoNaoPago"] = get_url_site() . '/carrinho/confirmacao/falha';
        $dados_envio["urlRedirecionamentoPago"] = get_url_site() . '/carrinho/confirmacao/sucesso';

        $dados_envio["valorDesconto"] = 0; //R$ 2,00

        $dados_envio["vencimentoBoleto"] = null;
        
        return $dados_envio;
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

}
