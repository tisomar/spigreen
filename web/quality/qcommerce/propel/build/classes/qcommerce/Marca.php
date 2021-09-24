<?php



/**
 * Skeleton subclass for representing a row from the 'QP1_MARCA' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Marca extends BaseMarca
{
    public $strPrefixFileName = 'MARC'; // prefixo a ser adicionado na frente de cada nome de arquivo salvo no servidor.
    public $strPathImg = '/arquivos/marcas/'; // endereço que será salvo as imagens no servidor. Será usado 'ROOT_PATH . $strPathImg'.
    public $strPhpNameImagem = 'Imagem'; // phpName da coluna que contém a imagem definido no arquivo schema do propel
    public $strNodeName = 'marcas';
    public $strPhpNameMedida = 'Medida'; // phpName da coluna que contém a imagem definido no arquivo schema do propel
    public $allowedExtentions = array('jpg', 'jpeg', 'png', 'gif');
    
    public function delete(PropelPDO $con = null)
    {
        try {
            $this->deleteImagem(); // apaga a imagem na chamada do metodo delete.
            parent::delete($con);
        } catch (Exception $e) {
            throw $e;
        }
        
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
    public function getThumb($strArgs, $arrAtributtes = array(), $boolUseImagemPadrao = true)
    {
        $arrAtributtes['alt'] = escape($this->getNome());
        $arrAtributtes['title'] = escape($this->getNome());

        return parent::getThumb($strArgs, $arrAtributtes, $boolUseImagemPadrao);
    }

    public function getThumbMedida($strArgs, $arrAtributtes = array(), $boolUseImagemPadrao = true)
    {
        $arrAtributtes['alt'] = $this->getNome();
        $arrAtributtes['title'] = $this->getNome();
        
        $backup = $this->strPhpNameImagem;
        $this->strPhpNameImagem = 'Medida';
        $response = parent::getThumb($strArgs, $arrAtributtes, $boolUseImagemPadrao);
        $this->strPhpNameImagem = $backup;
        return $response;
    }
    
    public function saveMedida($arrPostFile) {
        $backup = $this->strPhpNameImagem;
        $this->strPhpNameImagem = 'Medida';
        $response = parent::saveImagem($arrPostFile);
        $this->strPhpNameImagem = $backup;
        return $response;
    }
    public function deleteMedida() {
        $backup = $this->strPhpNameImagem;
        $this->strPhpNameImagem = 'Medida';
        $response = parent::deleteImagem();
        $this->strPhpNameImagem = $backup;
        return $response;
    }
    
    public function getUrlListagem() {
        return get_url_site() . '/produtos/marca/' .$this->getSlug();
    }

    // Fim controle de upload de imagem
}
