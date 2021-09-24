<?php

/**
 * Skeleton subclass for representing a row from the 'QP1_BANNER' table.
 *
 * Banners do sistema
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Banner extends BaseBanner
{
    const SIM = 1;
    const NAO = 0;
    const DESTAQUE = 'DESTAQUE';
    const LATERAL = 'LATERAL';
    const VANTAGEM = 'VANTAGEM';
    const APOIO = 'APOIO';
    const RODAPE = 'RODAPE';


    // Controle de upload de imagem
    public $strPrefixFileName = 'BANN'; // prefixo a ser adicionado na frente de cada nome de arquivo salvo no servidor.
    public $strPathImg = '/arquivos/banners/'; // endereço que será salvo as imagens no servidor. Será usado 'ROOT_PATH . $strPathImg'.
    public $strPhpNameImagem = 'Imagem'; // phpName da coluna que contém a imagem definido no arquivo schema do propel
    public $strNodeName = 'banners';
    public $allowedExtentions = array('gif', 'jpg', 'jpeg', 'png');

    public function myValidate(&$erros, $columns = null)
    {

        if ($this->getTipo() == BannerPeer::DESTAQUE) {

            if ($this->getImagemSm() == "") {
                $erros[] = "Você deve informar o banner para \"Small devices Tablets (≥768px)\"";
            }

            if ($this->getImagemMd() == "") {
                $erros[] = "Você deve informar o banner para \"Medium devices Desktops (≥992px)\"";
            }

            if ($this->getImagemLg() == "") {
                $erros[] = "Você deve informar o banner para \"Large devices Desktops (≥1200px)\"";
            }

        } elseif ($this->getTipo() == BannerPeer::VANTAGEM) {

            if ($this->getImagemMd() == "") {
                $erros[] = "Você deve informar o banner para \"Medium devices Desktops (≥992px)\"";
            }

        } elseif ($this->getTipo() == BannerPeer::APOIO) {

            if ($this->getImagemMd() == "") {
                $erros[] = "Você deve informar o banner para \"Medium devices Desktops (≥992px)\"";
            }

        } elseif ($this->getTipo() == BannerPeer::RODAPE) {

            if ($this->getImagemMd() == "") {
                $erros[] = "Você deve informar o banner para \"Medium devices Desktops (≥992px)\"";
            }

        }

        return parent::myValidate($erros, $columns);
    }

    public function delete(PropelPDO $con = null)
    {
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
    public function getThumb($strArgs, $arrAtributtes = array(), $boolUseImagemPadrao = true)
    {
        $arrAtributtes['alt'] = htmlspecialchars($this->getTitulo());
        $arrAtributtes['title'] = htmlspecialchars($this->getTitulo());

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

    /**
     * Retorna o texto de acordo se é para mostar ou nao o destaque
     * @return string
     */
    public function getDescMostrar()
    {
        return self::getDescConstMostrar($this->getMostrar());
    }

    /**
     * Retorna descricao para constanstes de mostrar
     * @param string $strMostrar
     * @return string
     */
    public static function getDescConstMostrar($strMostrar)
    {
        switch ($strMostrar)
        {
            case self::SIM : $strRet = 'Sim';
                break;
            case self::NAO : $strRet = 'Não';
                break;
            default : $strRet = '';
                break;
        }

        return $strRet;
    }

    public function setLink($v)
    {
        if (!is_null($v) && $v != '')
        {
            $v = add_scheme($v);
        }
        return parent::setLink($v);
    }

}
