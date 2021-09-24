<?php



/**
 * Skeleton subclass for representing a row from the 'QP1_GALERIA' table.
 *
 * Galerias de imagem
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Galeria extends BaseGaleria
{
    
    /**
     * Metodo que apaga as imagens de uma galeria qdo a galeria é excluida
     * @param PropelPDO $con 
     */
    public function delete(PropelPDO $con = null) {
        $this->getGaleriaArquivos()->delete();
        parent::delete($con);
    }
    
    
    
}
