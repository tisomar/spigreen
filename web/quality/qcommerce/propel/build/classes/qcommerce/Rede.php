<?php

/**
 * Skeleton subclass for representing a row from the 'QP1_REDE' table.
 *
 * Redes sociais pertencentes ao site
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Rede extends BaseRede {

    // Controle de upload de imagem
    public $strPrefixFileName = 'R'; // prefixo a ser adicionado na frente de cada nome de arquivo salvo no servidor.
    public $strPathImg = '/arquivos/redes/'; // endereço que será salvo as imagens no servidor. Será usado 'ROOT_PATH . $strPathImg'.
    public $strPhpNameImagem = 'Imagem'; // phpName da coluna que contém a imagem definido no arquivo schema do propel
    public $strNodeName = 'banners';
    public $allowedExtentions = array('jpg', 'png', 'jpeg', 'gif');

    public function delete(PropelPDO $con = null) {
        $this->deleteImagem(); // apaga a imagem na chamada do metodo delete.

        parent::delete($con);
    }

    /**
     * Função que sobreescreve os valores padrao da função getThumb do BaseObject
     * @date 06/04/2010
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
        //$arrAtributtes['link'] = $this->getLink();

        return parent::getThumb($strArgs, $arrAtributtes, $boolUseImagemPadrao);
    }

    // Fim controle de upload de imagem

    /**
     * Função que retorna uma thumb e se o destaque tiver link cadastrado coloca um link na thumb
     * @date 06/04/2010
     * @author Jaison Vargas Veneri
     * @see getThumb
     * @param string $strArgs
     * @param array $arrAtributtesLink
     * @param array $arrAtributtesImage
     * @param boolean $boolUseImagemPadrao
     */
    public function getThumbLink($strArgs, $arrAtributtesLink = array(), $arrAtributtesImage = array(), $boolUseImagemPadrao = true) {
        $strRetorno = "";

        if ($this->getLink() != '') {
            $arrAtributtesLink['href'] = $this->getLink();
            $strRetorno .= "<a " . get_atributes_html($arrAtributtesLink) . " >";
        }

        $strRetorno .= $this->getThumb($strArgs, $arrAtributtesImage, $boolUseImagemPadrao);

        if ($this->getLink() != '') {
            $strRetorno .= "</a>";
        }

        return $strRetorno;
    }

}
