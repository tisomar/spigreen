<?php

/**
 * Skeleton subclass for representing a row from the 'qp1_pedido_forma_pagamento' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class PedidoFormaPagamento extends BasePedidoFormaPagamento
{
    public $strPrefixFileName = 'COMPROVANTE_PAGAMENTO';
    public $strPathImg = '/admin/arquivos/';
    public $allowedExtentions = ['jpg', 'jpeg', 'png', 'gif'];

    public function getStatusLabel()
    {

        $options = array(
            PedidoFormaPagamentoPeer::STATUS_PENDENTE => array(
                'label' => 'warning',
                'icon' => 'icon-time',
                'title' => 'Aguardando pagamento'
            ),
            PedidoFormaPagamentoPeer::STATUS_APROVADO => array(
                'label' => 'success',
                'icon' => 'icon-ok',
                'title' => 'Pagamento Aprovado'
            ),
            PedidoFormaPagamentoPeer::STATUS_CANCELADO => array(
                'label' => 'danger',
                'icon' => 'icon-ban-circle',
                'title' => 'Pagamento Cancelado'
            ),
            PedidoFormaPagamentoPeer::STATUS_NEGADO => array(
                'label' => 'danger',
                'icon' => 'icon-ban-circle',
                'title' => 'Pagamento Negado'
            ),
        );

        extract($options[$this->getStatus()]);

        return label($title, $label, $icon);
    }

    public function getFormaPagamentoDescricao()
    {
        $formasPagamento = PedidoFormaPagamentoPeer::getFormasPagamento();
        return isset($formasPagamento[$this->getFormaPagamento()]) ? $formasPagamento[$this->getFormaPagamento()] : 'N/I';
    }

    public function getValorPorParcela()
    {
        $valorTotal = $this->getValorPagamento() ?? $this->getPedido()->getValorTotal();
        return round($valorTotal / $this->getNumeroParcelas(), 2);
    }

    public function getFormaPagamentoDescricaoCompletaAdminList()
    {
        switch ($this->getFormaPagamento()) :
            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO:
                return 'Boleto Bancário<br><span class="text-muted" data-toggle="tooltip" title="Vencimento">' . $this->getDataVencimento('d/m/Y').'</span>';
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CARTAO_CREDITO:
                return mb_strtolower($this->getBandeira()) . '<br><span class="text-muted">em ' . $this->getNumeroParcelas() . 'x</span>';
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO:
                return 'PagSeguro<br><span class="text-muted" data-toggle="tooltip" title="TID">' . $this->getTransacaoId() . '</span>';
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAYPAL:
                return 'PayPal<br><span class="text-muted" data-toggle="tooltip" title="TID">' . $this->getTransacaoId() . '</span>';
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_FATURAMENTO_DIRETO:
                return 'Faturamento Direto<br><span class="text-muted" data-toggle="tooltip" title="Opção">' . $this->getFaturamentoDiretoOpcao() . '</span>';
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_CARTAO_CREDITO:
                return 'PagSeguro - Cartão de Crédito<br><span class="text-muted" data-toggle="tooltip" title="TID">' . $this->getTransacaoId() . '</span>';
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_DEBITO_ONLINE:
                return 'PagSeguro - Débito Online<br><span class="text-muted" data-toggle="tooltip" title="TID">' . $this->getTransacaoId() . '</span>';
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_BOLETO:
                return 'PagSeguro - Boleto<br><span class="text-muted" data-toggle="tooltip" title="TID">' . $this->getTransacaoId() . '</span>';
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_ITAUSHOPLINE:
                return 'Itaú Shopline<br>';
                break;
            
            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS:
                return 'Bônus<br>';
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS_CLIENTE_PREFERENCIAL:
                return 'Pontos<br>';
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BONUS_FRETE:
                return 'Bônus Frete<br>';
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_EM_LOJA:
                return 'Pagamento em Loja<br>';
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_CREDITO:
                return 'Cielo - Cartão de Crédito<br>';
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_DEBITO:
                return 'Cielo - Cartão de Débito<br>';
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_BOLETO_BB:
                return 'Boleto Banco do Brasil<br>';
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_BOLETO_BRADESCO:
                return 'Boleto Bradesco<br>';
                break;

            case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_TRANSFERENCIA:
                return 'Transferência<br>';
                break;

            default:
                break;
        endswitch;
    }

    public function getBandeiraCielo()
    {

        $retorno = null;
        switch ($this->getBandeira()){
            case 'visa':
                $retorno = 'Visa';
                break;
            case 'mastercard':
                $retorno = 'Master';
                break;
            case 'elo':
                $retorno = 'Elo';
                break;
            case 'discover':
                $retorno = 'Discover';
                break;
            case 'diners':
                $retorno = 'Diners';
                break;
            case 'amex':
                $retorno = 'Amex';
                break;
            case 'hipercard':
                $retorno = 'Hipercard';
                break;

        }

        return $retorno;
    }

    public function getDataAprovacao($format = 'Y-m-d H:i:s')
    {
        if ($this->getStatus() == PedidoFormaPagamentoPeer::STATUS_APROVADO) :
            if (!empty(parent::getDataAprovacao($format))) :
                return parent::getDataAprovacao($format);
            endif;

            return $this->getUpdatedAt($format);
        endif;

        return null;
    }

    public function save(PropelPDO $con = null)
    {
        if ($this->isColumnModified(PedidoFormaPagamentoPeer::STATUS)) :
            if ($this->getStatus() == PedidoFormaPagamentoPeer::STATUS_APROVADO) :
                $this->setDataAprovacao(new DateTime());
            else :
                $this->setDataAprovacao(null);
            endif;
        endif;

        return parent::save($con);
    }

    public function getFilenameComprovante()
    {
        return "{$this->strPrefixFileName}_{$this->getPedidoId()}";
    }
}
