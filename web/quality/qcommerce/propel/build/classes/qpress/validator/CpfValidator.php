<?php
//require_once 'propel/validator/BasicValidator.php';


class CpfValidator implements BasicValidator {
    
    private $arrValidos = array(
        
    );

    private $arrInvalidos = array(
        '00000000000',
        '11111111111',
        '22222222222',
        '33333333333',
        '44444444444',
        '55555555555',
        '66666666666',
        '77777777777',
        '88888888888',
        '99999999999'
    );

    public function isValid (ValidatorMap $map, $str) {

        return $this->validaCPF($str);

    }

    public function validaCPF($strCPF) {

        $strCPF = preg_replace('/[^0-9]/', "", $strCPF);

        foreach($this->arrValidos as $strValido){
            if ($strValido == $strCPF){
                return true;
            }
        }

        foreach($this->arrInvalidos as $strInvalido){
            if ($strInvalido == $strCPF){
                return false;
            }
        }

        $boolValido = false;
        if (strlen($strCPF) == 11) {

            $arrCPF = array();
            for ($i = 0; $i < 11; $i++) {
                $arrCPF[$i] = (int)$strCPF[$i];
            }
            $arrDV = array(0, 0);

            for ($i = 10; $i >= 2; $i--) {
                $arrDV[0] += ($i * ($arrCPF[10-$i]));
            }
            $arrDV[0] = (11 - ($arrDV[0] % 11));
            if ($arrDV[0] >= 10) {
                $arrDV[0] = 0;
            }

            for ($i = 11; $i >= 2; $i--) {
                $arrDV[1] += ($i * ($arrCPF[11-$i]));
            }
            $arrDV[1] = (11 - ($arrDV[1] % 11));
            if ($arrDV[1] >= 10) {
                $arrDV[1] = 0;
            }

            $boolValido = (($arrCPF[9] == $arrDV[0]) && ($arrCPF[10] == $arrDV[1]));
        }
        return $boolValido;
    }
}


?>
