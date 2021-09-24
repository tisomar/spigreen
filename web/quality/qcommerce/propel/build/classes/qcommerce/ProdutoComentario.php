<?php

/**
 * Skeleton subclass for representing a row from the 'QP1_PRODUTO_COMENTARIO' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ProdutoComentario extends BaseProdutoComentario
{
    const STATUS_APROVADO = 'APROVADO';
    const STATUS_PENDENTE = 'PENDENTE';

    public function myValidate(&$erros, $columns = null) {

        if ($this->getNota() < 1 || $this->getNota() > 5) {
            $erros[] = "Por favor, selecione uma estrela para avaliar.";
        }

        return parent::myValidate($erros, $columns);
    }

    /**
     * Seta informações prédefinidas de Produtos, Clientes e configurações
     * necessárias para o cadastro do comentário
     */
    public function setDefaultInformation(Cliente $objCliente, Produto $objProduto)
    {

        $this->setCliente($objCliente);
        $this->setNome($objCliente->getNomeCompleto());
        $this->setEmail($objCliente->getEmail());

        $this->setProdutoId($objProduto->getId());

        $this->setData(date('Y-m-d H:i:s'));
        $this->setIP($_SERVER['REMOTE_ADDR']);

    }
    
    public function getImgStatus()
    {
        switch ($this->status)
        {
            case self::STATUS_APROVADO : $status = 'icon-ok';
                $title = 'Aprovado';
                break;
            case self::STATUS_PENDENTE : $status = 'icon-time';
                $title = 'Pendente';
                break;

            default:break;
        }

        return "<span title='$title' class='$status'></span>";
    }

    public function getDescStatus()
    {
        $strRet = '';
        
        if ($this->getStatus() == self::STATUS_APROVADO)
        {
            $strRet = "Aprovado";
        }
        elseif ($this->getStatus() == self::STATUS_PENDENTE)
        {
            $strRet = "Pendente";
        }

        return $strRet;
    }

    public function getStatusLabel() {

        if ($this->getStatus() == self::STATUS_APROVADO) {
            $status = 'success';
            $icon = 'icon-ok';
        } elseif ($this->getStatus() == self::STATUS_PENDENTE) {
            $status = 'warning';
            $icon = 'icon-time';
        }

        return sprintf('<h4><label class="label label-%s"><i class="%s"></i> <span class="">%s</span></label></h4>', $status, $icon, $this->getDescStatus());

    }

    public function getStatusClass() {
        if ($this->getStatus() == self::STATUS_APROVADO) {
            $status = 'success';
        } elseif ($this->getStatus() == self::STATUS_PENDENTE) {
            $status = 'warning';
        }

        return $status;
    }

    /**
     * Retorna o email de quem fez o comentario. Busca o email do cliente caso for um comentario de cliente
     * @return string
     */
    public function getEmail()
    {
        if ($this->getCliente() instanceof Cliente)
        {
            return $this->getCliente()->getEmail();
        }

        return parent::getEmail();

    }

    /**
     * Retorna o nome de quem fez o comentário
     * Verifica primeiro se existe um cliente vinculado ao comentário, se sim, pega o
     * nome atualizado do cliente, caso contrário, pega o nome vinculado ao comentário
     * 
     * @return string
     */
    public function getNome()
    {
        if ($this->getCliente() instanceof Cliente)
        {
            return $this->getCliente()->getNomeCompleto();
        }
        
        return parent::getNome();
    }
    
}
