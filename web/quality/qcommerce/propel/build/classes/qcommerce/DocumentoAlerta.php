<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_documento_alerta' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class DocumentoAlerta extends BaseDocumentoAlerta
{

    /**
     * Tipos de mensagens dos Alertas.
     */
    const DOC_TERMO_USO                     = 'termos_uso';
    const DOC_POLITICA_PRIVACIDADE          = 'politica_privacidade';
    const DOC_POLITICA_PAGAMENTOS           = 'politica_pagamentos';
    const DOC_POLITICA_ENTREGA              = 'politica_entrega';
    const DOC_POLITICA_TROCA                = 'politica_troca';
    const DOC_COMUNICADOS_OCIFIAIS          = 'comunicados_oficiais';
    const DOC_ANIVERSARIANTES               = 'aniversariantes';

    /**
     * Tipos de destinatários dos Alertas.
     */

    const DOC_DEST_ALL                      = 'todos';
    const DOC_DEST_COMBO                    = 'combo';
    const DOC_DEST_NOT_COMBO                = 'not_combo';
    const DOC_DEST_ATIVO                    = 'ativo';
    const DOC_DEST_NOT_ATIVO                = 'not_ativo';
    const DOC_DEST_NIVEL                    = 'nivel_mensal';
    const DOC_DEST_CLIENTE                  = 'cliente';
    const DOC_NOVO_CLIENTE                  = 'novo_cliente';


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

    protected static $destinatarios = array(
        self::DOC_DEST_ALL,
        self::DOC_DEST_COMBO,
        self::DOC_DEST_NOT_COMBO,
        //self::DOC_DEST_NIVEL,
        self::DOC_DEST_CLIENTE,
        self::DOC_DEST_ATIVO,
        self::DOC_DEST_NOT_ATIVO,
        self::DOC_NOVO_CLIENTE,
    );

    protected static $destinatariosDesc = array(
        self::DOC_DEST_ALL => 'Todos',
        self::DOC_DEST_COMBO => 'Clientes com plano',
        self::DOC_DEST_NOT_COMBO => 'Clientes sem plano',
        self::DOC_DEST_ATIVO => 'Clientes ativos no mês',
        self::DOC_DEST_NOT_ATIVO => 'Clientes inativos no mês',
        //self::DOC_DEST_NIVEL => 'Classificação mensal',
        self::DOC_DEST_CLIENTE => 'Cliente(s) específico(s)',
        self::DOC_NOVO_CLIENTE => 'Novos cadastrados',
    );

    /**
     *
     * Retorna o Tipo com base no salvo no banco. Consulta na base registrada de constantes
     *
     * @return mixed|string
     */

    public function getTipoDesc(){

        $retorno = $this->getTipoMensagem();

        if (isset(self::$tiposDesc[$this->getTipoMensagem()])) {
            $retorno = self::$tiposDesc[$this->getTipoMensagem()];
        }

        return $retorno;
    }

    /**
     *
     * Retorna a descrição de todos os tipos registrados em constantes
     *
     * @return array
     */
    public function getAllTipoDesc(){

        return self::$tiposDesc;
    }

    /**
     *
     * Retorna o Destinatário com base no salvo no banco. Consulta na base registrada de constantes
     *
     * @return mixed|string
     */

    public function getDestinatariosDesc(){

        $retorno = $this->getTipoDest();

        if (isset(self::$destinatariosDesc[$this->getTipoDest()])) {
            $retorno = self::$destinatariosDesc[$this->getTipoDest()];
        }

        return $retorno;
    }

    public function getSomenteLeituraDesc(){

        $retorno = $this->getSomenteLeitura();

        $arrRetorno = array('1' => 'Sim', '0' => 'Não');

        return $arrRetorno[$retorno];
    }

    /**
     *
     * Retorna a descrição de todos os destinatários registrados em constantes
     *
     * @return array
     */

    public function getAllDestinatariosDesc(){

        return self::$destinatariosDesc;
    }

    public function setDataEnvio($v)
    {
        if (is_string($v)) {
            $v = DateTime::createFromFormat('d/m/Y', $v);
            if (!$v) {
                throw new InvalidArgumentException('Data inválida.');
            }
            $v->setTime(0, 0, 0);
        }

        return parent::setDataEnvio($v);
    }

    /**
     * @param $cliente Cliente
     * @throws PropelException
     */
    public function mostraMensagem($cliente)
    {
        $mostraMensagem = false;

        switch ($this->tipo_dest) :
            case 'todos':
                $mostraMensagem = true;

                break;
            case 'combo':
                if ($cliente->getPlano()) :
                    $mostraMensagem = true;
                endif;

                break;
            case 'not_combo':
                if (!$cliente->getPlano()) :
                    $mostraMensagem = true;
                endif;

                break;
            case 'ativo':
                $gerenciador = new GerenciadorPlanoCarreira(Propel::getConnection(), $cliente);

                if ($cliente->getPlano() && $gerenciador->getStatusAtivacao(Date('m'), Date('Y'))) :
                    $mostraMensagem = true;
                endif;

                break;
            case 'not_ativo':
                $gerenciador = new GerenciadorPlanoCarreira(Propel::getConnection(), $cliente);

                if ($cliente->getPlano() && !$gerenciador->getStatusAtivacao(Date('m'), Date('Y'))) :
                    $mostraMensagem = true;
                endif;

                break;
            case 'cliente':
                $clientes = explode(',', $this->getIdClientesStr());

                if (in_array($cliente->getId(), $clientes)) :
                    $mostraMensagem = true;
                endif;

                break;
            case 'novo_cliente':
                $dataAtual = new DateTime();

                if ($dataAtual->format('Y-m-d') == $cliente->getCreatedAt('Y-m-d')) :
                    $mostraMensagem = true;
                endif;
                break;
        endswitch;

        return $mostraMensagem;
    }

}
