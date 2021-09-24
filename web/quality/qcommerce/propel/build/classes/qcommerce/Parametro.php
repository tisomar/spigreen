<?php

/**
 * Classe para padronizar a utilização de parâmetros do sistema
 * 
 * @author Felipe Corrêa
 * @since 07/03/2013
 * 
 * @package    propel.generator.qcommerce
 */
class Parametro extends BaseParametro
{
    public $strPrefixFileName   = '';
    public $strPathImg          = '/arquivos/configuracao/';
    public $strPhpNameImagem    = 'Valor';
    public $allowedExtentions   = array('png', 'gif', 'jpg', 'jpeg');
    public $randomName          = false;

    public function setValor($v) {

        if ($this->getType() == 'MONEY') {
            if (!is_numeric($v))
            {
                $v = str_replace(array('R$', ' '), null, $v);
                $v = format_number($v, UsuarioPeer::LINGUAGEM_INGLES);
            }
        }

        return parent::setValor($v);
    }

    public function getValorFormatado() {

        if ($this->getType() == 'MONEY') {
            return 'R$ ' . format_number($this->getValor(), UsuarioPeer::LINGUAGEM_PORTUGUES);
        }

        return parent::getValor();

    }

    /**
     * Neste caso específico, há necessidade de refazer o cache das categorias.
     *
     * @return void
     */
    public function post_save_limite_exibicao_subcategorias()
    {
        CategoriaPeer::refazerCache();
    }

    public function post_save_categorias_modo_ordenacao()
    {
        $root = CategoriaQuery::create()->findRoot();
        $root->sortChildrens();
        CategoriaPeer::refazerCache();
    }

}
