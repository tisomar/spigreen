<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_plano_carreira' table.
 *
 * Contem a tabela com os níveis do plano de carreira
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class PlanoCarreira extends BasePlanoCarreira
{

    public $strPrefixFileName = 'PLANO_CARREIRA';
    public $strPathImg = '/admin/arquivos/'; // NAO ACEITA OUTRO DIRETORIO RECEM CRIADO
    public $strPhpNameImagem = 'Imagem';
    public $allowedExtentions = array('jpg', 'jpeg', 'png', 'gif');

    public function setValorPremio($v) 
    {
        if (!is_numeric($v)) {
            $v = str_replace(array('R$', ' ', '%'), null, $v);
            $v = format_number($v, UsuarioPeer::LINGUAGEM_INGLES);
        }
        
        return parent::setValorPremio($v);
    }

    /**
     *
     * Delete Imagens
     *
     * @param PropelPDO|null $con
     * @throws Exception
     */
    public function delete(PropelPDO $con = null)
    {
        try {
            $this->deleteImagem();
            parent::delete($con);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * Pegar o Thumb da imagem
     *
     * @param string $strArgs
     * @param array $arrAtributtes
     * @param bool $boolUseImagemPadrao
     * @return string
     */

    public function getThumb($strArgs, $arrAtributtes = array(), $boolUseImagemPadrao = false)
    {
        $arrAtributtes['alt'] = escape($this->getNome());
        $arrAtributtes['title'] = escape($this->getNome());
        return parent::getThumb($strArgs, $arrAtributtes, $boolUseImagemPadrao);
    }

    /**
     *
     * Função que retorna uma thumb e se o destaque tiver link cadastrado coloca um link na thumb
     *
     * @see getThumb
     * @param $strArgs
     * @param array $arrAtributtesLink
     * @param array $arrAtributtesImage
     * @param bool $boolUseImagemPadrao
     * @return string
     */
    public function getThumbLink($strArgs, $arrAtributtesLink = array(), $arrAtributtesImage = array(), $boolUseImagemPadrao = true)
    {

        if (!isset($arrAtributtesLink['href'])) {
            if ($this->getLink() != '') {
                $arrAtributtesLink['href'] = $this->getLink();
            } else {
                $arrAtributtesLink['href'] = 'javascript:;';
            }
        }

        $arrAtributtesLink['title'] = isset($arrAtributtesLink['title']) ? $arrAtributtesLink['title'] : $this->getTitulo();
        $arrAtributtesLink['target'] = isset($arrAtributtesLink['target']) ? $arrAtributtesLink['target'] : $this->getTarget();

        $arrAtributtesLink['onclick'] = sprintf("return counterBanner(%d);", $this->getId());
        $arrAtributtesLink['data-reference'] = $this->getId();

        return sprintf("<a %s>%s</a>",
            get_atributes_html($arrAtributtesLink),
            $this->getThumb($strArgs, $arrAtributtesImage, $boolUseImagemPadrao));

    }

}
