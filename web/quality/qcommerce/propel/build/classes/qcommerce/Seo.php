<?php

/**
 * Skeleton subclass for representing a row from the 'QP1_SEO' table.
 *
 * Informações para os motores de busca
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Seo extends BaseSeo {

    const HOME = 'HOME';
    const EMPRESA = 'EMPRESA';
    const PRODUTO = 'PRODUTO';
    const PROMOCAO = 'PROMOCAO';
    const FAQ = 'FAQ';
    const CADASTRO = 'CADASTRO';
    const LOGIN = 'LOGIN';
    const CARRINHO = 'CARRINHO';
    const NOTICIA = 'NOTICIA';
    const CENTRAL = 'CENTRAL';
    const CONTATO = 'CONTATO';

    public static function getArrayTipo() {
        return array(
            self::HOME => SeoPeer::getDescTipo(self::HOME),
            self::EMPRESA => SeoPeer::getDescTipo(self::EMPRESA),
            self::PRODUTO => SeoPeer::getDescTipo(self::PRODUTO),
            self::PROMOCAO => SeoPeer::getDescTipo(self::PROMOCAO),
            self::FAQ => SeoPeer::getDescTipo(self::FAQ),
            self::CADASTRO => SeoPeer::getDescTipo(self::CADASTRO),
            self::LOGIN => SeoPeer::getDescTipo(self::LOGIN),
            self::CARRINHO => SeoPeer::getDescTipo(self::CARRINHO),
            self::NOTICIA => SeoPeer::getDescTipo(self::NOTICIA),
            self::CENTRAL => SeoPeer::getDescTipo(self::CENTRAL),
            self::CONTATO => SeoPeer::getDescTipo(self::CONTATO),
            
        );
    }

    /**
     * Sobreescrita da funcao para salvar registroid como nulo no banco de dados
     * @param integer $v
     */
    public function setRegistroId($v) {
        if ($v == '') {
            $v = null;
        }

        parent::setRegistroId($v);
    }

    /**
     * Retorna descricao para o tipo do cadastro
     * @return string
     */
    public function getDescTipo() {
        return SeoPeer::getDescTipo($this->getTipo());
    }

    /**
     * Retorna classe do propel para o tipo se existir
     * @return string
     */
    public function getClassTipo() {
        return SeoPeer::getClassTipo($this->getTipo());
    }

    /**
     * Retorna um obj do registro se ele estiver vinculado com algum registro
     * @return mixed
     */
    public function getRegistro() {
        $strClass = $this->getClassTipo() . 'Peer';
        if (class_exists($strClass)) {
            return call_user_func(array($strClass, 'retrieveByPk'), $this->getRegistro());
        }

        return false;
    }

    /**
     * Retorna o nome do registro
     * @return string
     */
    public function getNomeRegistro() {
        $strNome = '-';

        $objRegistro = $this->getRegistro();

        if ($objRegistro) {
            $strFuncDescricao = SeoPeer::getFuncDescTipo($this->getTipo());
            $strNome = $objRegistro->$strFuncDescricao();
        }

        return $strNome;
    }

    /**
     * Metodo para setar as variaveis de seo
     */
    public function setVariables(&$strTitle, &$strDescription, &$strKeyWords) {
        $strTitle = $this->getTitulo();
        $strDescription = resumo($this->getDescricao(), 147);
        $strKeyWords = $this->getPalavrasChave();
    }

}
