<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_configuracao_pontuacao_mensal' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ConfiguracaoPontuacaoMensalPeer extends BaseConfiguracaoPontuacaoMensalPeer
{
    public static function getDescricaoExtrato(){

        $objConfiguracao = ConfiguracaoPontuacaoMensalQuery::create()->findOneById(1);

        return $objConfiguracao->getDescricaoExtrato();
    }

    public static function getValorMinimoCompraMensal()
    {
        $configPontuacaoMensal = ConfiguracaoPontuacaoMensalQuery::create()
            ->findOneById(1);
        
        return $configPontuacaoMensal->getValorCompra() ?? 0;
    }

    public static function getValorMinimoPontosMensal()
    {
        $configPontuacaoMensal = ConfiguracaoPontuacaoMensalQuery::create()
            ->findOneById(1);

        return $configPontuacaoMensal->getValorPontos() ?? 0;
    }
}
