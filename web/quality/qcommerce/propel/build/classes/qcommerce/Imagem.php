<?php



/**
 * Skeleton subclass for representing a row from the 'QP1_IMAGEM' table.
 *
 * Tabela que grava as imagens da galeria
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Imagem extends BaseImagem
{
    // Controle de upload de imagem

    public $strPrefixFileName = 'IMAG'; // prefixo a ser adicionado na frente de cada nome de arquivo salvo no servidor.
    public $strPathImg = '/arquivos/galeria/'; // endereï¿½o que serï¿½ salvo as imagens no servidor. Serï¿½ usado 'ROOT_PATH . $strPathImg'.
    public $strPhpNameImagem = 'Imagem'; // phpName da coluna que contï¿½m a imagem definido no arquivo schema do propel
    public $strNodeName     = 'galerias';

    public function delete(PropelPDO $con = null) {
        $this->deleteImagem(); // apaga a imagem na chamada do metodo delete.

        parent::delete($con);
    }

    /**
     * Funï¿½ï¿½o que sobreescreve os valores padrao da funï¿½ï¿½o getThumb do BaseObject
     * @date 03/01/2010
     * @author Jaison Vargas Veneri
     * @see BaseObject->getThumb
     * @param string $strArgs
     * @param array $arrAtributtes
     * @param boolean $boolUseImagemPadrao
     * @return string
     */
    public function getThumb($strArgs, $arrAtributtes = array(), $boolUseImagemPadrao = true) {
        $arrAtributtes['alt'] = escape($this->getNome());
        $arrAtributtes['title'] = escape($this->getNome());

        return parent::getThumb($strArgs, $arrAtributtes, $boolUseImagemPadrao);
    }

    // Fim controle de upload de imagem


    public static function getPathImgStatic() {
        $objImagem = new Imagem();
        return $objImagem->strPathImg;
    }
    
    
}
