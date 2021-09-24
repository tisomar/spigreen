<?php

/**
 * Contem o resultado de uma chamada a API do Mailforweb.
 *
 * @author Garlini
 */
class MailforwebResult
{
    /**
     * Chamada foi um sucesso?
     *
     * @var bool
     */
    protected $sucesso;
    

    /**
     * Resultado da chamada.
     *
     * @var mixed
     */
    protected $result;

    /**
     * Contem os eventuais erros retornados pelo MFW.
     *
     * @var array
     */

    protected $erros;

    /**
     * Contem os eventuais retorno pelo MFW.
     *
     * @var array
     */

    protected $retorno;

    public function __construct($sucesso = false, $result = null, $erros = array(), $retorno = array())
    {
        $this->sucesso = $sucesso;
        $this->result = $result;
        $this->erros = $erros;
        $this->retorno = $retorno;
    }
    
    /**
     *
     * @return bool
     */
    function getSucesso()
    {
        return $this->sucesso;
    }

    /**
     *
     * @return array
     */
    function getErros()
    {
        return $this->erros;
    }

    /**
     *
     * @return array
     */
    function getRetorno()
    {
        return $this->retorno;
    }

    /**
     *
     * @param bool $sucesso
     */
    function setSucesso($sucesso)
    {
        $this->sucesso = (bool)$sucesso;
    }

    /**
     *
     * @param array $erros
     */
    function setErros($erros)
    {
        $this->erros = $erros;
    }

    /**
     *
     * @param string $erro
     * @return \MailforwebResult
     */
    public function addErro($erro)
    {
        $this->erros[] = $erro;
        return $this;
    }
    
    
    /**
     * Retorna true caso a chamada possua mensagem de erros.
     *
     * @return bool
     */
    public function hasErros()
    {
        return count($this->erros) > 0;
    }
    
    /**
     * Returna true se a chamada foi um sucesso, false senão.
     *
     * @return bool
     */
    public function isSucesso()
    {
        return (bool)$this->sucesso;
    }
    
    /**
     *
     * @return mixed
     */
    function getResult()
    {
        return $this->result;
    }
    
    /**
     *
     * @param mixed $result
     */
    function setResult($result)
    {
        $this->result = $result;
    }
}
