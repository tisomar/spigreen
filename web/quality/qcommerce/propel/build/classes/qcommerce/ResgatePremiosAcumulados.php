<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_resgate_premios_acumulados' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ResgatePremiosAcumulados extends BaseResgatePremiosAcumulados
{
   const SITUACAO_PENDENTE     = 'PENDENTE';
   const SITUACAO_EFETUADO     = 'EFETUADO';
   const SITUACAO_NAOEFETUADO  = 'NAOEFETUADO';
   const SITUACAO_EXTORNADO    = 'EXTORNADO';
    
   const TIPO_PREMIO       =  'PREMIO';
   const TIPO_DINHEIRO     =  'DINHEIRO';
   
   public function preInsert(\PropelPDO $con = null) 
   {
      $ret = parent::preInsert($con);
      
      if (!$this->getData()) {
         $this->setData(new Datetime());
      }
      
      return $ret;
   }
   
   /**
    * 
    * @return string
    */
   public function getSituacaoDesc()
   {
      $list = ResgatePremiosAcumuladosPeer::getSituacaoList();
      $situacao = $this->getSituacao();
      
      return isset($list[$situacao]) ? $list[$situacao] : '';
   }
}
