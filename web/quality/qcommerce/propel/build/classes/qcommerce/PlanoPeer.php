<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_plano' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class PlanoPeer extends BasePlanoPeer
{
    CONST SIM = 1;
    CONST NAO = 0;

    /**
     * @param $column string
     * @return array
     * @throws Exception
     */
    public static function getValueSet($column)
    {

        switch ($column)
        {
            case PlanoPeer::PARTICIPA_EXPANSAO:
            case PlanoPeer::PARTICIPA_PRODUTIVIDADE:
            case PlanoPeer::PARTICIPA_FIDELIDADE:
            case PlanoPeer::PARTICIPA_PARTICIPACAO_LUCROS:
            case PlanoPeer::PARTICIPA_PLANO_CARREIRA:
            case PlanoPeer::PARTICIPA_LIDERANCA:
            case PlanoPeer::PARTICIPA_DESEMPENHO:
            case PlanoPeer::PARTICIPA_DESTAQUE:
            case PlanoPeer::PARTICIPA_INCENTIVO:
            case PlanoPeer::PARTICIPA_CLIENTE_PREFERENCIAL:
            case PlanoPeer::PLANO_CLIENTE_PREFERENCIAL:

                $response = array(
                    self::SIM => 'Sim',
                    self::NAO => 'Não',
                );

                break;

            default:
                throw new Exception('variable $column not found!');
        }

        return $response;
    }
    
    public static function getProdutoList()
    {
        $options = array(
            null => 'Selecione...'
        );

        $query = ProdutoQuery::create()->orderByNome();
        foreach ($query->find() as $produto) { /* @var $produto Produto */
            $options[$produto->getId()] = $produto->getNome();
        }

        return $options;
    }
    
    /**
     * 
     * @return Plano|null
     */
    public static function findPlanoGratuito()
    { 
        return PlanoQuery::create()
                ->filterByProdutoId(null)
                ->findOne();
    }
}
