<?php

namespace QPress\Frete\DataResponse;

abstract class AbstractDataResponseFrete implements DataResponseFreteInterface
{

    public $endereco = null;
    private $valor;
    private $prazo;
    private $erro;
    private $disponivel = true;

    public function getEndereco()
    {
        return $this->endereco;
    }

    public function setEndereco($endereco)
    {
        $this->endereco = $endereco;
    }

    public function getValor()
    {
        return $this->valor;
    }

    public function setValor($v)
    {
        $this->valor = $v;
    }

    public function getPrazo()
    {
        return $this->prazo;
    }

    public function setPrazo($v)
    {
        $this->prazo = $v;
    }
    
    public function getPrazoExtenso()
    {
        if ($this->getPrazo() > 1)
        {
           return $this->getPrazo() . ' dias úteis'; 
        }
        else
        {
            return $this->getPrazo() . ' dia útil'; 
        }
    }

    public function getErro()
    {
        return $this->erro;
    }

    public function setErro($v)
    {
        $this->erro = $v;
    }

    public function hasErro()
    {
        return !is_null($this->getErro());
    }

    public function setDisponivel($v) {
        $this->disponivel = (boolean) $v;
    }

    public function isDisponivel() {
        return $this->disponivel;
    }



}
