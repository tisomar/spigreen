<?php

use \QPress\Frete\Services\Correios\Servicos\Correios04669;
use \QPress\Frete\Services\Correios\Servicos\Correios04162;
use \QPress\Frete\FreteInterface;

class CalculoCorreiosDefault {

    private $tipoFrete;

    public function __construct(FreteInterface $tipoFrete)
    {
        $this->tipoFrete = $tipoFrete;
    }

    public function getDisponivel(Endereco $endereco = null, $cep = null)
    {
        // Frete PAC para Goiás está indisponível
        if ($endereco) :
            return $this->tipoFrete instanceof Correios04669 && $endereco->getCidade()->getEstado()->getSigla() === 'GO' ? false : true;
        elseif ($cep) :
            return $this->tipoFrete instanceof Correios04669 && (($cep >= 72800000 && $cep <= 72999999) ||
            ($cep >= 73700000 && $cep <= 76799999)) ? false : true;
        endif;

        return true;
    }

    public function getPrazoFrete(Endereco $endereco)
    {
        switch ($endereco->getCidade()->getEstado()->getSigla()) {
            case 'AC':
                return $this->tipoFrete instanceof Correios04669 ? 21 : 6;
                break;
            case 'AL':
                return $this->tipoFrete instanceof Correios04669 ? 13 : 6;
                break;
            case 'AM':
                return $this->tipoFrete instanceof Correios04669 ? 25 : 6;
                break;
            case 'AP':
                return $this->tipoFrete instanceof Correios04669 ? 24 : 10;
                break;
            case 'BA':
                return $this->tipoFrete instanceof Correios04669 ? 8 : 4;
                break;
            case 'CE':
                return $this->tipoFrete instanceof Correios04669 ? 12 : 6;
                break;
            case 'DF':
                return $this->tipoFrete instanceof Correios04669 ? 7 : 1;
                break;
            case 'ES':
                return $this->tipoFrete instanceof Correios04669 ? 9 : 5;
                break;
            case 'GO':
                return $this->tipoFrete instanceof Correios04669 ? 0 : 2;
                break;
            case 'MA':
                return $this->tipoFrete instanceof Correios04669 ? 13 : 8;
                break;
            case 'MG':
                return $this->tipoFrete instanceof Correios04669 ? 7 : 5;
                break;
            case 'MS':
                return $this->tipoFrete instanceof Correios04669 ? 8 : 4;
                break;
            case 'MT':
                return $this->tipoFrete instanceof Correios04669 ? 11 : 7;
                break;
            case 'PA':
                return $this->tipoFrete instanceof Correios04669 ? 17 : 8;
                break;
            case 'PB':
                return $this->tipoFrete instanceof Correios04669 ? 12 : 7;
                break;
            case 'PE':
                return $this->tipoFrete instanceof Correios04669 ? 9 : 6;
                break;
            case 'PI':
                return $this->tipoFrete instanceof Correios04669 ? 13 : 5;
                break;
            case 'PR':
                return $this->tipoFrete instanceof Correios04669 ? 7 : 3;
                break;
            case 'RJ':
                return $this->tipoFrete instanceof Correios04669 ? 9 : 4;
                break;
            case 'RN':
                return $this->tipoFrete instanceof Correios04669 ? 11 : 8;
                break;
            case 'RO':
                return $this->tipoFrete instanceof Correios04669 ? 18 : 7;
                break;
            case 'RS':
                return $this->tipoFrete instanceof Correios04669 ? 14 : 7;
                break;
            case 'RR':
                return $this->tipoFrete instanceof Correios04669 ? 24 : 4;
                break;
            case 'SC':
                return $this->tipoFrete instanceof Correios04669 ? 8 : 4;
                break;
            case 'SE':
                return $this->tipoFrete instanceof Correios04669 ? 9 : 4;
                break;
            case 'SP':
                return $this->tipoFrete instanceof Correios04669 ? 6 : 3;
                break;
            case 'TO':
                return $this->tipoFrete instanceof Correios04669 ? 11 : 5;
                break;
        }
    }

    public function getPrazoFretePorCEP($cep)
    {
        // AC
        if ($cep >= 69900000 && $cep <= 69999999) :
            return $this->tipoFrete instanceof Correios04669 ? 21 : 6;
        endif;

        // AL
        if ($cep >= 57000000 && $cep <= 57999999) :
            return $this->tipoFrete instanceof Correios04669 ? 13 : 6;
        endif;

        // AM
        if (($cep >= 69000000 && $cep <= 69299999) || ($cep >= 69400000 && $cep <= 69899999)) :
            return $this->tipoFrete instanceof Correios04669 ? 25 : 6;
        endif;

        // AP
        if ($cep >= 68900000 && $cep <= 68999999) :
            return $this->tipoFrete instanceof Correios04669 ? 24 : 10;
        endif;

        // BA
        if ($cep >= 40000000 && $cep <= 48999999) :
            return $this->tipoFrete instanceof Correios04669 ? 8 : 4;
        endif;

        // CE
        if ($cep >= 60000000 && $cep <= 63999999) :
            return $this->tipoFrete instanceof Correios04669 ? 12 : 6;
        endif;

        // DF
        if (($cep >= 70000000 && $cep <= 72799999) || ($cep >= 73000000 && $cep <= 73699999)) :
            return $this->tipoFrete instanceof Correios04669 ? 7 : 1;
        endif;

        // ES
        if ($cep >= 29000000 && $cep <= 29999999) :
            return $this->tipoFrete instanceof Correios04669 ? 9 : 5;
        endif;

        // GO
        if (($cep >= 72800000 && $cep <= 72999999) || ($cep >= 73700000 && $cep <= 76799999)) :
            return $this->tipoFrete instanceof Correios04669 ? 0 : 2;
        endif;

        // MA
        if ($cep >= 65000000 && $cep <= 65999999) :
            return $this->tipoFrete instanceof Correios04669 ? 13 : 8;
        endif;

        // MG
        if ($cep >= 30000000 && $cep <= 39999999) :
            return $this->tipoFrete instanceof Correios04669 ? 7 : 5;
        endif;

        // MS
        if ($cep >= 79000000 && $cep <= 79999999) :
            return $this->tipoFrete instanceof Correios04669 ? 8 : 4;
        endif;

        // MT
        if ($cep >= 78000000 && $cep <= 78899999) :
            return $this->tipoFrete instanceof Correios04669 ? 11 : 7;
        endif;

        // PA
        if ($cep >= 66000000 && $cep <= 68899999) :
            return $this->tipoFrete instanceof Correios04669 ? 17 : 8;
        endif;

        //PB
        if ($cep >= 58000000 && $cep <= 58999999) :
            return $this->tipoFrete instanceof Correios04669 ? 12 : 7;
        endif;

        // PE
        if ($cep >= 50000000 && $cep <= 56999999) :
            return $this->tipoFrete instanceof Correios04669 ? 9 : 6;
        endif;

        // PI
        if ($cep >= 64000000 && $cep <= 64999999) :
            return $this->tipoFrete instanceof Correios04669 ? 13 : 5;
        endif;

        // PR
        if ($cep >= 80000000 && $cep <= 87999999) :
            return $this->tipoFrete instanceof Correios04669 ? 7 : 3;
        endif;

        // RJ
        if ($cep >= 20000000 && $cep <= 28999999) :
            return $this->tipoFrete instanceof Correios04669 ? 9 : 4;
        endif;

        // RN
        if ($cep >= 59000000 && $cep <= 59999999) :
            return $this->tipoFrete instanceof Correios04669 ? 11 : 8;
        endif;

        // RO
        if ($cep >= 76800000 && $cep <= 76999999) :
            return $this->tipoFrete instanceof Correios04669 ? 18 : 7;
        endif;

        // RR
        if ($cep >= 69300000 && $cep <= 69399999) :
            return $this->tipoFrete instanceof Correios04669 ? 24 : 4;
        endif;

        // RS
        if ($cep >= 90000000 && $cep <= 99999999) :
            return $this->tipoFrete instanceof Correios04669 ? 14 : 7;
        endif;

        // SC
        if ($cep >= 88000000 && $cep <= 89999999) :
            return $this->tipoFrete instanceof Correios04669 ? 8 : 4;
        endif;

        // SE
        if ($cep >= 49000000 && $cep <= 49999999) :
            return $this->tipoFrete instanceof Correios04669 ? 9 : 4;
        endif;

        // SP
        if ($cep >= 01000000 && $cep <= 19999999) :
            return $this->tipoFrete instanceof Correios04669 ? 6 : 3;
        endif;

        // TO
        if ($cep >= 77000000 && $cep <= 77999999) :
            return $this->tipoFrete instanceof Correios04669 ? 11 : 5;
        endif;
    }

    public function getPrazoFreteExtenso(Endereco $endereco = null, $cep = null)
    {
        if ($endereco) :
            return  $this->getPrazoFrete($endereco) === 1 ? $this->getPrazoFrete($endereco). ' dia útil' : $this->getPrazoFrete($endereco) . ' dias úteis';
        elseif (cep) :
            return $this->getPrazoFretePorCEP($cep) === 1 ? $this->getPrazoFretePorCEP($cep) . ' dia útil' : $this->getPrazoFretePorCEP($cep) . ' dias úteis';
        endif;

        return '';
    }

    public function getValorFrete(Endereco $endereco)
    {
        switch ($endereco->getCidade()->getEstado()->getSigla()) {
            case 'AC':
                return $this->tipoFrete instanceof Correios04669 ? 33.05 : 70.58;
                break;
            case 'AL':
                return $this->tipoFrete instanceof Correios04669 ? 27.55 : 59.28;
                break;
            case 'AM':
                return $this->tipoFrete instanceof Correios04669 ? 33.05 : 70.58;
                break;
            case 'AP':
                return $this->tipoFrete instanceof Correios04669 ? 33.05 : 70.58;
                break;
            case 'BA':
                return $this->tipoFrete instanceof Correios04669 ? 61.31 : 79.11;
                break;
            case 'CE':
                return $this->tipoFrete instanceof Correios04669 ? 27.55 : 59.28;
                break;
            case 'DF':
                return $this->tipoFrete instanceof Correios04669 ? 16.41 : 20.66;
                break;
            case 'ES':
                return $this->tipoFrete instanceof Correios04669 ? 27.55 : 59.28;
                break;
            case 'GO':
                return $this->tipoFrete instanceof Correios04669 ? 0 : 10.14;
                break;
            case 'MA':
                return $this->tipoFrete instanceof Correios04669 ? 27.55 : 59.28;
                break;
            case 'MG':
                return $this->tipoFrete instanceof Correios04669 ? 20.57 : 39.52;
                break;
            case 'MS':
                return $this->tipoFrete instanceof Correios04669 ? 20.57 : 39.52;
                break;
            case 'MT':
                if ($this->tipoFrete instanceof Correios04669) :
                    return 20.57;
                elseif ($this->tipoFrete instanceof Correios04162) :
                    // 39.52
                    return 35;
                else :
                    return 29;
                endif;
                break;
            case 'PA':
                return $this->tipoFrete instanceof Correios04669 ? 61.31 : 79.11;
                break;
            case 'PB':
                return $this->tipoFrete instanceof Correios04669 ? 61.31 : 79.11;
                break;
            case 'PE':
                return $this->tipoFrete instanceof Correios04669 ? 27.55 : 59.28;
                break;
            case 'PI':
                return $this->tipoFrete instanceof Correios04669 ? 27.55 : 59.28;
                break;
            case 'PR':
                return $this->tipoFrete instanceof Correios04669 ? 27.55 : 59.28;
                break;
            case 'RJ':
                return $this->tipoFrete instanceof Correios04669 ? 44.66 : 68.98;
                break;
            case 'RN':
                return $this->tipoFrete instanceof Correios04669 ? 33.05 : 70.58;
                break;
            case 'RO':
                return $this->tipoFrete instanceof Correios04669 ? 27.55 : 59.28;
                break;
            case 'RS':
                return $this->tipoFrete instanceof Correios04669 ? 61.31 : 79.11;
                break;
            case 'RR':
                return $this->tipoFrete instanceof Correios04669 ? 33.05 : 70.58;
                break;
            case 'SC':
                return $this->tipoFrete instanceof Correios04669 ? 61.31 : 79.11;
                break;
            case 'SE':
                return $this->tipoFrete instanceof Correios04669 ? 27.55 : 59.28;
                break;
            case 'SP':
                return $this->tipoFrete instanceof Correios04669 ? 22.96 : 50.82;
                break;
            case 'TO':
                return $this->tipoFrete instanceof Correios04669 ? 18.36 : 28.23;
                break;
        }
    }

    public function getValorFretePorCEP($cep)
    {
        // AC
        if ($cep >= 69900000 && $cep <= 69999999) :
            return $this->tipoFrete instanceof Correios04669 ? 33.05 : 70.58;
        endif;

        // AL
        if ($cep >= 57000000 && $cep <= 57999999) :
            return $this->tipoFrete instanceof Correios04669 ? 27.55 : 59.28;
        endif;

        // AM
        if (($cep >= 69000000 && $cep <= 69299999) || ($cep >= 69400000 && $cep <= 69899999)) :
            return $this->tipoFrete instanceof Correios04669 ? 33.05 : 70.58;
        endif;

        // AP
        if ($cep >= 68900000 && $cep <= 68999999) :
            return $this->tipoFrete instanceof Correios04669 ? 33.05 : 70.58;
        endif;

        // BA
        if ($cep >= 40000000 && $cep <= 48999999) :
            return $this->tipoFrete instanceof Correios04669 ? 61.31 : 79.11;
        endif;

        // CE
        if ($cep >= 60000000 && $cep <= 63999999) :
            return $this->tipoFrete instanceof Correios04669 ? 27.55 : 59.28;
        endif;

        // DF
        if (($cep >= 70000000 && $cep <= 72799999) || ($cep >= 73000000 && $cep <= 73699999)) :
            return $this->tipoFrete instanceof Correios04669 ? 16.41 : 20.66;
        endif;

        // ES
        if ($cep >= 29000000 && $cep <= 29999999) :
            return $this->tipoFrete instanceof Correios04669 ? 27.55 : 59.28;
        endif;

        // GO
        if (($cep >= 72800000 && $cep <= 72999999) || ($cep >= 73700000 && $cep <= 76799999)) :
            return $this->tipoFrete instanceof Correios04669 ? 0 : 10.14;
        endif;

        // MA
        if ($cep >= 65000000 && $cep <= 65999999) :
            return $this->tipoFrete instanceof Correios04669 ? 27.55 : 59.28;
        endif;

        // MG
        if ($cep >= 30000000 && $cep <= 39999999) :
            return $this->tipoFrete instanceof Correios04669 ? 20.57 : 39.52;
        endif;

        // MS
        if ($cep >= 79000000 && $cep <= 79999999) :
            return $this->tipoFrete instanceof Correios04669 ? 20.57 : 39.52;
        endif;

        // MT
        if ($cep >= 78000000 && $cep <= 78899999) :
            return $this->tipoFrete instanceof Correios04669 ? 20.57 : 39.52;
        endif;

        // PA
        if ($cep >= 66000000 && $cep <= 68899999) :
            return $this->tipoFrete instanceof Correios04669 ? 61.31 : 79.11;
        endif;

        //PB
        if ($cep >= 58000000 && $cep <= 58999999) :
            return $this->tipoFrete instanceof Correios04669 ? 61.31 : 79.11;
        endif;

        // PE
        if ($cep >= 50000000 && $cep <= 56999999) :
            return $this->tipoFrete instanceof Correios04669 ? 27.55 : 59.28;
        endif;

        // PI
        if ($cep >= 64000000 && $cep <= 64999999) :
            return $this->tipoFrete instanceof Correios04669 ? 27.55 : 59.28;
        endif;

        // PR
        if ($cep >= 80000000 && $cep <= 87999999) :
            return $this->tipoFrete instanceof Correios04669 ? 27.55 : 59.28;
        endif;

        // RJ
        if ($cep >= 20000000 && $cep <= 28999999) :
            return $this->tipoFrete instanceof Correios04669 ? 44.66 : 68.98;
        endif;

        // RN
        if ($cep >= 59000000 && $cep <= 59999999) :
            return $this->tipoFrete instanceof Correios04669 ? 33.05 : 70.58;
        endif;

        // RO
        if ($cep >= 76800000 && $cep <= 76999999) :
            return $this->tipoFrete instanceof Correios04669 ? 27.55 : 59.28;
        endif;

        // RR
        if ($cep >= 69300000 && $cep <= 69399999) :
            return $this->tipoFrete instanceof Correios04669 ? 61.31 : 79.11;
        endif;

        // RS
        if ($cep >= 90000000 && $cep <= 99999999) :
            return $this->tipoFrete instanceof Correios04669 ? 33.05 : 70.58;
        endif;

        // SC
        if ($cep >= 88000000 && $cep <= 89999999) :
            return $this->tipoFrete instanceof Correios04669 ? 61.31 : 79.11;
        endif;

        // SE
        if ($cep >= 49000000 && $cep <= 49999999) :
            return $this->tipoFrete instanceof Correios04669 ? 27.55 : 59.28;
        endif;

        // SP
        if ($cep >= 01000000 && $cep <= 19999999) :
            return $this->tipoFrete instanceof Correios04669 ? 22.96 : 50.82;
        endif;

        // TO
        if ($cep >= 77000000 && $cep <= 77999999) :
            return $this->tipoFrete instanceof Correios04669 ? 18.36 : 28.23;
        endif;
    }

}