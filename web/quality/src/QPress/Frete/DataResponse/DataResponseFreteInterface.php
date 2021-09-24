<?php

namespace QPress\Frete\DataResponse;

interface DataResponseFreteInterface
{
    public function getValor();

    public function setValor($v);

    public function getPrazo();

    public function setPrazo($v);

    public function isDisponivel();
}
