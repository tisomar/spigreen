<?php

/**
 * Skeleton subclass for performing query and update operations on the 'QP1_SEO' table.
 *
 * Informações para os motores de busca
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class SeoPeer extends BaseSeoPeer
{

    CONST PAGINA_HOME = 'HOME';
    CONST PAGINA_EMPRESA = 'EMPRESA';
    CONST PAGINA_PRODUTO = 'PRODUTO';
    CONST PAGINA_CATEGORIA = 'CATEGORIA';
    CONST PAGINA_PROMOCAO = 'PROMOCAO';
    CONST PAGINA_FAQ = 'FAQ';
    CONST PAGINA_CADASTRO = 'CADASTRO';
    CONST PAGINA_LOGIN = 'LOGIN';
    CONST PAGINA_CARRINHO = 'CARRINHO';
    CONST PAGINA_CONTATO = 'CONTATO';

    public static function getPaginas()
    {
        return array(
            self::PAGINA_HOME => 'Página Inicial',
            self::PAGINA_EMPRESA => 'Página Empresa',
            self::PAGINA_PRODUTO => 'Página Produtos',
            self::PAGINA_CATEGORIA => 'Página Categorias',
            self::PAGINA_FAQ => 'Página Perguntas Frequentes',
            self::PAGINA_CADASTRO => 'Página Cadastro',
            self::PAGINA_LOGIN => 'Página Login',
            self::PAGINA_CARRINHO => 'Página Carrinho de Compras',
            self::PAGINA_CONTATO => 'Página Contato',
        );
    }

    public static function getRegistrosByPagina($pagina, $c = false)
    {
        
        $registros = array(
            'Padrão'
        );

        switch ($pagina)
        {
            case self::PAGINA_PRODUTO:

                $registros_db = ProdutoQuery::create()->select(array('Id', 'Nome'))->orderByNome()->find()->toArray();

                foreach ($registros_db as $arrObject)
                {
                    $registros[$arrObject['Id']] = $arrObject['Nome'];
                }

                break;

            case self::PAGINA_CATEGORIA:

                $registros_db = CategoriaQuery::create()->select(array('Id', 'Nome'))->orderByNrLft()->filterByNrLvl(array('min' => 1))->find();

                foreach ($registros_db as $arrObject)
                {
                    $registros[$arrObject['Id']] = $arrObject['Nome'];
                }

                break;

            default:
                $registros = null;
                break;
        }

        return $registros;
    }

    // ---------------------------------------------------------------------------------------

    /**
     * retorna uma tag select para campo tipo
     * @param string $strValueSelected
     * @param array $arrOptions
     * @param array $arrAttributtes
     * @return string
     */
    public static function getFormSelectMostrar($strValueSelected, $arrOptions = false, $arrAttributtes = array())
    {
        $arrAttributtes['name'] = isset($arrAttributtes['name']) ? $arrAttributtes['name'] : 'seo[TIPO]';
        $arrAttributtes['id'] = isset($arrAttributtes['id']) ? $arrAttributtes['id'] : 'id_tipo';
        $arrAttributtes['title'] = isset($arrAttributtes['title']) ? $arrAttributtes['title'] : 'Indica tipo do registro';
        $arrAttributtes['class'] = isset($arrAttributtes['tooltip']) ? $arrAttributtes['tooltip'] : 'tooltip';

        if ($arrOptions === false)
        {
            $arrOptions = Seo::getArrayTipo();
        }

        return get_form_select($arrOptions, $strValueSelected, $arrAttributtes);
    }

    /**
     * Retorna descricao para o tipo do cadastro
     * @return string
     */
    public static function getDescTipo($strDesc)
    {
        $strRet = '';

        if ($strDesc == SEO::HOME)
        {
            $strRet = 'Home';
        }
        elseif ($strDesc == SEO::EMPRESA)
        {
            $strRet = 'Empresa';
        }
        elseif ($strDesc == SEO::PRODUTO)
        {
            $strRet = 'Produto';
        }
        elseif ($strDesc == SEO::PROMOCAO)
        {
            $strRet = 'Promoções';
        }
        elseif ($strDesc == SEO::FAQ)
        {
            $strRet = 'FAQ';
        }
        elseif ($strDesc == SEO::CADASTRO)
        {
            $strRet = 'Cadastro';
        }
        elseif ($strDesc == SEO::LOGIN)
        {
            $strRet = 'Login';
        }
        elseif ($strDesc == SEO::CARRINHO)
        {
            $strRet = 'Carrinho';
        }
        elseif ($strDesc == SEO::NOTICIA)
        {
            $strRet = 'Noticia';
        }
        elseif ($strDesc == SEO::CENTRAL)
        {
            $strRet = 'Central do Cliente';
        }
        elseif ($strDesc == SEO::CONTATO)
        {
            $strRet = 'Contato';
        }

        return $strRet;
    }

    /**
     * Retorna função para ser usada para pegar a descricao do registro
     * @return string
     */
    public static function getFuncDescTipo($strTipo)
    {
        $strRet = '';

        if (($strTipo == SEO::PRODUTO) or
                ($strTipo == SEO::PROMOCAO))
        {
            $strRet = 'getNome';
        }
        else if ($strTipo == SEO::NOTICIA)
        {
            $strRet = 'getTitulo';
        }

        return $strRet;
    }

    /**
     * Retorna todos os registros dependendo do tipo passado de parametro
     * @param string $strTipo
     * @param Criteria $c
     * @return array
     */
    public static function getRegistrosByTipo($strTipo, $c = false)
    {
        new Seo();

        $strClass = self::getClassTipo($strTipo) . 'Peer';

        if (!$c)
        {
            $c = new Criteria();
            if ($strTipo == 'PRODUTO')
            {
//                $c->add(ProdutoPeer::ATIVO, 1);
            }
        }

        if (class_exists($strClass))
        {
            return call_user_func(array($strClass, 'doSelect'), $c);
        }

        return array();
    }

    /**
     * Retorna pelo banco o objeto seo pelo tipo e registro id
     * @param string $strTipo
     * @param integer $intRegistroId
     * @return Seo
     */
    public static function retrieveByTipoRegistroId($strTipo, $intRegistroId = null)
    {
        $c = new Criteria();

        if ($intRegistroId == '')
        {
            $intRegistroId = null;
        }

        $c->add(self::TIPO, $strTipo);
        $c->add(self::REGISTRO_ID, $intRegistroId);

        // por definição so existira apenas uma tupla com tipo e codigo
        return self::doSelectOne($c);
    }

    public static function cadastrarSeoProduto(Produto $objProduto)
    {
        
        $objSeo = SeoQuery::create()
                ->filterByRegistroId($objProduto->getId())
                ->filterByPagina(SeoPeer::PAGINA_PRODUTO)
            ->findOneOrCreate();

        // Limita a 65 caracteres devido a ser o que está no banco de dados do SEO
        $objSeo->setMetaTitle(resumo($objProduto->getNome(), 65, ''));
        
        // Limita a 150 caracteres e remove tags html e quebras de linha
        $objSeo->setMetaDescription(resumo(limpar_html($objProduto->getDescricao()), 150, ''));
        
        // Limita a 255 caracteres, remove palavras comuns (a,o,de,da), limpa html, quebras de linha e quebra os espaços por vírgula
        $objSeo->setMetaKeywords(resumo(limpar_html(implode(',', explode(' ', remover_palavras_comuns($objProduto->getNome())))), 255, ''));

        if ($objSeo->myValidate($erros) && !$erros)
        {
            $objSeo->save();
            return true;
        }
        else
        {
            return $erros;
        }
        
    }

}
