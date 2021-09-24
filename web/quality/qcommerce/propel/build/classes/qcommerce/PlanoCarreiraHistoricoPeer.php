<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_plano_carreira_historico' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class PlanoCarreiraHistoricoPeer extends BasePlanoCarreiraHistoricoPeer
{
    public static function getQualificacaoCliente(Cliente $cliente, $mes, $ano)
    {
        $query = PlanoCarreiraHistoricoQuery::create()
            ->filterByCliente($cliente)
            ->filterByMes($mes)
            ->filterByAno($ano)
            ->findOne();

        return $query ? $query->getPlanoCarreira() : null;
    }
}
