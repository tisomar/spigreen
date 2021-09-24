<?php

/**
 * Skeleton subclass for representing a row from the 'QP1_CONTEUDO' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Conteudo extends BaseConteudo
{
    const AGENDA_CERTIFIQUE              = 15;
    const AGENDA_SUPORTE                 = 12;
    const AGENDA_SMS                     = 13;
    const AGENDA_CONTATO                 = 14;

    // Controle de upload de imagem
    public $strPrefixFileName = 'CONT'; // prefixo a ser adicionado na frente de cada nome de arquivo salvo no servidor.
    public $strPathImg = '/arquivos/conteudo/'; // endereço que será salvo as imagens no servidor. Será usado 'ROOT_PATH . $strPathImg'.
    public $strPhpNameImagem = 'Imagem'; // phpName da coluna que contém a imagem definido no arquivo schema do propel

    // Tipos de conteúdo
    const TIPO_CONTEUDO_PAGINA = 'PAGINA';
    const TIPO_CONTEUDO_TEXTO_LOCALIZADO = 'TEXTO_LOCALIZADO';

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

    /**
     * Retorna a cor a ser exibida para a linha do registro na área administrativa
     * com base no tipo de conteúdo do registro.
     */
    public function getAdminCorLinha()
    {
        $cor = "";

        switch ($this->getTipoConteudo())
        {
            case self::TIPO_CONTEUDO_PAGINA:
                $cor = '#fff';
                break;

            case self::TIPO_CONTEUDO_TEXTO_LOCALIZADO:
                $cor = '#F0F0F0';
                break;
        }

        return $cor;
    }

    /**
     * Retorna a cor a ser exibida para a linha do registro na área administrativa
     * com base no tipo de conteúdo do registro.
     */
    public function getAdminHtmlCorLinha()
    {

        $cor = $this->getAdminCorLinha();
        $htmlCor = "";

        if (!empty($cor))
        {
            $htmlCor = ' style="background-color: ' . $cor . '; border-top: 1px solid #FFFFFF; border-bottom: 1px solid #DDDDDD";';
        }

        return $htmlCor;
    }
}
