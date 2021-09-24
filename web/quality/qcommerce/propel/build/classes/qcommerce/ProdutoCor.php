<?php

/**
 * Skeleton subclass for representing a row from the 'qp1_produto_cor' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ProdutoCor extends BaseProdutoCor {

    public $strPrefixFileName = '';
    public $strPathImg = '/arquivos/cores/';
    public $strPhpNameImagem = 'Imagem';
    public $strNodeName = '';
    public $allowedExtentions = array('gif', 'jpg', 'jpeg', 'png');

    public function getThumb($strArgs, $arrAtributtes = array(), $boolUseImagemPadrao = true) {
        $arrAtributtes['alt'] = escape($this->getNome());
        $arrAtributtes['title'] = escape($this->getNome());
        return parent::getThumb($strArgs, $arrAtributtes, $boolUseImagemPadrao);
    }

    public function getBoxColor($width, $height, $addMargin = true) {
        if ($this->isImagemExists()) {
            $box = $this->getThumb(sprintf('width=%s&height=%s&cropratio=1', $width, $height));
        } else {
            $box = sprintf("<div style='width:%spx;height:%spx;background:%s;' title='%s'>&nbsp;</div>", $width, $height, $this->getRgb(), $this->getNome());
        }

        if ($addMargin) {
            return sprintf('<div style="width:%spx;height:%spx; background: #FFF; border: 1px solid #d7d7d7; padding: 3px; border-bottom: 2px solid #d7d7d7; overflow: hidden;">%s</div>', $width+8, $height+8, $box);
        } else {
            return $box;
        }
    }

    public function getBackground($width, $height) {
        if ($this->isImagemExists()) {
            return "background: url('" . $this->getUrlImageResize('width=' . $width . '&height=' . $height . '&cropratio=1:1') . "');";
        } else {
            return "background: " . $this->getRgb() . ";";
        }
    }

    public function preSave(PropelPDO $con = null) {
        if ($this->getRgb() != '') {
            $this->deleteImagem();
        }

        return parent::preSave($con);
    }

}
