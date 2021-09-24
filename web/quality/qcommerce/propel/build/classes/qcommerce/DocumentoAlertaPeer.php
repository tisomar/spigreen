<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_documento_alerta' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class DocumentoAlertaPeer extends BaseDocumentoAlertaPeer
{

    const DOC_TERMO_USO                     = 'termos_uso';
    const DOC_POLITICA_PRIVACIDADE          = 'politica_privacidade';
    const DOC_POLITICA_PAGAMENTOS           = 'politica_pagamentos';
    const DOC_POLITICA_ENTREGA              = 'politica_entrega';
    const DOC_POLITICA_TROCA                = 'politica_troca';
    const DOC_COMUNICADOS_OCIFIAIS          = 'comunicados_oficiais';
    const DOC_ANIVERSARIANTES               = 'aniversariantes';


    protected static $tipos = array(
        self::DOC_TERMO_USO,
        self::DOC_POLITICA_PRIVACIDADE,
        self::DOC_POLITICA_PAGAMENTOS,
        self::DOC_POLITICA_ENTREGA,
        self::DOC_POLITICA_TROCA,
        self::DOC_COMUNICADOS_OCIFIAIS,
        self::DOC_ANIVERSARIANTES,
    );

    protected static $tiposDesc = array(
        self::DOC_TERMO_USO => 'Termos e Condições de Uso',
        self::DOC_POLITICA_PRIVACIDADE => 'Política de Privacidade e Segurança',
        self::DOC_POLITICA_PAGAMENTOS => 'Política de Pagamentos',
        self::DOC_POLITICA_ENTREGA => 'Política de Entrega',
        self::DOC_POLITICA_TROCA => 'Política de Trocas e Devoluções',
        self::DOC_COMUNICADOS_OCIFIAIS => 'Mensagem ou comunicados oficiais',
        self::DOC_ANIVERSARIANTES => 'Aniversariantes'
    );

    public static function getTipoDesc($tipoMensagem){

        $retorno = $tipoMensagem;

        if (isset(self::$tiposDesc[$tipoMensagem])) {
            $retorno = self::$tiposDesc[$tipoMensagem];
        }

        return $retorno;
    }

}
