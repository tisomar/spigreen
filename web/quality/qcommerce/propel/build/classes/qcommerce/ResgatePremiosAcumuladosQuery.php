<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_resgate_premios_acumulados' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ResgatePremiosAcumuladosQuery extends BaseResgatePremiosAcumuladosQuery
{
   public function filterByNomeCliente($nomeCliente = null, $comparison = null) 
   {
       if (null === $comparison) {
           $comparison = Criteria::LIKE;
           $nomeCliente = '%'.$nomeCliente.'%';
       }
       
       $this->useClienteQuery()
                   ->filterByNome($nomeCliente, $comparison)
                   ->_or()
                   ->filterByNomeFantasia($nomeCliente, $comparison)
             ->endUse();  
               
       return $this;
   }
}
