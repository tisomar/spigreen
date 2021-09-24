<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_galeria_arquivo' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class GaleriaArquivo extends BaseGaleriaArquivo
{
    public $strPrefixFileName = '';
    public $strPathImg = '/arquivos/galeria-arquivo/';
    public $strPhpNameImagem = 'Nome';
    public $allowedExtentions = array('gif', 'jpg', 'jpeg', 'png');

    /**
     * Deleta as imagens do servidor
     * @param PropelPDO $con
     */
    public function delete(PropelPDO $con = null) {
        $this->getArquivo()->delete($con);
        return parent::delete($con);
    }
}
