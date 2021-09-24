<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_plano_carreira_historico' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class PlanoCarreiraHistorico extends BasePlanoCarreiraHistorico
{
    public function getVolumeTotalGrupo()
    {
        return $this->getTotalPontosPessoais() + $this->getTotalPontosAdesao() + $this->getTotalPontosRecompra();
    }
}
