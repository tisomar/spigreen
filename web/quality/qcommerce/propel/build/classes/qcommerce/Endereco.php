<?php

/**
 * Skeleton subclass for representing a row from the 'QP1_ENDERECO' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Endereco extends BaseEndereco {

    const PRINCIPAL = 'PRINCIPAL';
    const ENTREGA = 'ENTREGA';

    public function getEnderecoCompleto() {
        $complemento = (!is_empty($this->getComplemento()) ? ' - ' . $this->getComplemento() : '');

        $enderecoCompleto = "<p>";
        $enderecoCompleto .= escape($this->getCep()) . " | ";
        $enderecoCompleto .= escape($this->getLogradouro()) . ", " . escape($this->getNumero());
        $enderecoCompleto .= $complemento;
        $enderecoCompleto .= "</p>";
        $enderecoCompleto .= "<p>";
        $enderecoCompleto .= escape($this->getBairro()) . ", " . escape($this->getCidade()->getNome()) . " - " . escape($this->getCidade()->getEstado()->getSigla());
        $enderecoCompleto .= "</p>";

        return $enderecoCompleto;
    }

    public function sprintf($format = '', $char = '%') {

        $map = array(
            'identificacao' => $this->getIdentificacao(),
            'logradouro' => $this->getLogradouro(),
            'numero' => $this->getNumero(),
            'bairro' => $this->getBairro(),
            'cep' => $this->getCep(),
            'complemento' => $this->getComplemento(),
            'cidade' => $this->getCidade()->getNome(),
            'estado' => $this->getCidade()->getEstado()->getNome(),
            'uf' => $this->getCidade()->getEstado()->getSigla(),
        );

        return sprintf2($format, $map);
    }

    public function getCep($clear = false) {
        return $clear ? clear_cep(parent::getCep()) : parent::getCep();
    }

    public function getCepBling() {

        $cep = $this->getCep();

        $antes = substr($cep, 0, 2);

        $depois = substr($cep, 2);

        return $antes.'.'.$depois;
    }

    /**
     * @todo implementar recurso de determina se é o endereço principal
     * @return boolean
     */
    public function isPrincipal() {
        return false; //$this->getTipo() == Endereco::PRINCIPAL;
    }

    public function getEnderecoSemFormatacao()
    {
        $complemento = ($this->getComplemento()) ? escape($this->getComplemento()) : '';

        $estado = $this->getCidade()->getEstado();
        $nomeEstado = ('XX' == $estado->getSigla()) ? $estado->getNome() : $estado->getSigla();

        $endereco =
            escape($this->getLogradouro()) . ",".escape($this->getNumero())." ". $complemento ." - ".escape($this->getBairro()). chr(10).
            escape($this->getCidade()->getNome()) . " - " . escape($nomeEstado) . " - ". escape($this->getCep()) . chr(10);

        return $endereco;
    }

/**
     * @param PropelPDO|null $con
     * @return bloolean
     */

    public function preSave(PropelPDO $con = null)
    {

        $enderecos = EnderecoQuery::create()->findByClienteId($this->getClienteId());

        if(count($enderecos) == 0){
            $this->setEnderecoPrincipal(1);
        }

        return parent::preSave();
    }

    public function removePrincipalAddress(PropelPDO $con = null)
    {
        $enderecos = EnderecoQuery::create()
                            ->filterById($this->getId(),Criteria::NOT_EQUAL)
                            ->findByClienteId($this->getClienteId());

        if(count($enderecos) > 0){
            foreach ($enderecos as $endereco){
                /** @var $endereco Endereco */
                $endereco->setEnderecoPrincipal(false);
                $endereco->save();
            }
        }
    }
}
