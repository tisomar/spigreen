<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_estoque_produto' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class EstoqueProduto extends BaseEstoqueProduto
{
    public function getOperacaoList(){
        return array(
            'ENTRADA' => 'Entrada',
            'SAIDA' => 'Saída',
        );
    }


    public function getOperacaoDesc(){
        $list = $this->getOperacaoList();
        return isset($list[$this->getOperacao()]) ? $list[$this->getOperacao()] : $this->getOperacao();
    }

    public function myValidate(&$erros, $columns = null){
        
        if ($this->getQuantidade() <= 0) :
            $erros[] = 'Quantidade menor ou igual a zero.';
        endif;

        if ($this->getOperacao() == 'SAIDA' && $this->getCentroDistribuicaoId()) :
            if($this->getQuantidade() > EstoqueProdutoPeer::getQuantidadeEstoqueDisponivel($this->getProdutoVariacao(), $this->getCentroDistribuicaoId())) :
                $erros[] = 'Quantidade para retirada do estoque maior que estoque atual.';
            endif;
        endif;

        if (!$this->getCentroDistribuicaoId()) :
            $erros[] = 'Centro de distribuição deve ser informado.';
        endif;

        return parent::myValidate($erros, $columns);
    }

}
