<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_cartao_cielo_dados' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class CartaoCieloDados extends BaseCartaoCieloDados
{
    /**
     * Get the [validade_mes] column value.
     *
     * @return string
     */
    public function getValidade()
    {
        $mes = strlen($this->getValidadeMes()) == 1 ? '0'.$this->getValidadeMes() : $this->getValidadeMes();
        return $mes."/".$this->getValidadeAno();
    }

    /**
     * Get the [json_analisis_risco] column value.
     *
     * @param PropelPDO|null $con
     * @return string
     * @throws PropelException
     */
    public function save(PropelPDO $con = null)
    {
        $numero = substr($this->getNumero(), 0, 6).'*-****-'.substr($this->getNumero(), -4);
        $this->setNumero($numero);
        $this->setCodigo("");
        return parent::save($con);
    }

}
