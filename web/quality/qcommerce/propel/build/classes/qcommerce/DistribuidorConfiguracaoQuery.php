<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_distribuidor_configuracao' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class DistribuidorConfiguracaoQuery extends BaseDistribuidorConfiguracaoQuery
{
    /**
     *
     * @param Cliente $cliente
     * @return \DistribuidorConfiguracao
     */
    public static function getConfiguracaoDistribuidor(Cliente $cliente)
    {
        $configuracao = self::create()->findOneByCliente($cliente);
        if (!$configuracao) {
            $configuracao = new DistribuidorConfiguracao();
            $configuracao->setCliente($cliente);
        }
        return $configuracao;
    }
}
