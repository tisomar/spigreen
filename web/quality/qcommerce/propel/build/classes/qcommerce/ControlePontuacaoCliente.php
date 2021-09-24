<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_controle_pontuacao_cliente' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ControlePontuacaoCliente extends BaseControlePontuacaoCliente
{
    public function getPontosTotais()
    {
        return ($this->pontos_pessoais ?? 0) + ($this->pontos_adesao ?? 0) + ($this->pontos_recompra ?? 0);
    }
}
