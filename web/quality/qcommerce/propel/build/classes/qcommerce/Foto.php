<?php



/**
 * Skeleton subclass for representing a row from the 'QP1_FOTO' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Foto extends BaseFoto
{
    // Controle de upload de imagem
    public $strPrefixFileName = 'FOTO'; // prefixo a ser adicionado na frente de cada nome de arquivo salvo no servidor.
    public $strPathImg = '/arquivos/produtos/imagens_adicionais/'; // endereço que será salvo as imagens no servidor. Será usado 'ROOT_PATH . $strPathImg'.
    public $strPhpNameImagem = 'Imagem'; // phpName da coluna que contém a imagem definido no arquivo schema do propel
    public $strNodeName = 'fotos';
    public $allowedExtentions = array('png', 'gif', 'jpg', 'jpeg');

    /**
     * Função que sobreescreve os valores padrao da função getThumb do BaseObject
     * @date 03/01/2010
     * @author Jaison Vargas Veneri
     * @see BaseObject->getThumb
     * @param string $strArgs
     * @param array $arrAtributtes
     * @param boolean $boolUseImagemPadrao
     * @return string
     */
    public function getThumb($strArgs, $arrAtributtes = array(), $boolUseImagemPadrao = true)
    {
        $arrAtributtes['alt'] = escape($this->getLegenda());
        $arrAtributtes['title'] = escape($this->getLegenda());

        return parent::getThumb($strArgs, $arrAtributtes, $boolUseImagemPadrao);
    }

    public function delete(PropelPDO $con = null)
    {
        $this->deleteImagem(); // apaga a imagem na chamada do metodo delete.

        parent::delete($con);
    }

    public function setCor($v) {
        if ($v == '') {
            $v = null;
        }
        return parent::setCor($v);
    }
}
