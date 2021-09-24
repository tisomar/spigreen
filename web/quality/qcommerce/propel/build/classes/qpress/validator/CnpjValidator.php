<?php
//require_once 'propel/validator/BasicValidator.php';


class CnpjValidator implements BasicValidator {

    private $arrValidos = array(
        
    );

    private $arrInvalidos = array(
        '00000000000000',
        '11111111111111',
        '22222222222222',
        '33333333333333',
        '44444444444444',
        '55555555555555',
        '66666666666666',
        '77777777777777',
        '88888888888888',
        '99999999999999'
    );

    public function isValid (ValidatorMap $map, $str)
    {
        if ($this->validaCNPJ($str)){
            return true;
        }
        else{
            return false;
        }
    }
 
    function validaCNPJ($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', "", $cnpj);

        if (strlen($cnpj) <> 14){
            return false;
        }

        foreach($this->arrValidos as $strValido){
            if ($strValido == $cnpj){
                return true;
            }
        }

        foreach($this->arrInvalidos as $strInvalido){
            if ($strInvalido == $cnpj){
                return false;
            }
        }

        $soma1 = ($cnpj[0] * 5) +
                ($cnpj[1] * 4) +
                ($cnpj[2] * 3) +
                ($cnpj[3] * 2) +
                ($cnpj[4] * 9) +
                ($cnpj[5] * 8) +
                ($cnpj[6] * 7) +
                ($cnpj[7] * 6) +
                ($cnpj[8] * 5) +
                ($cnpj[9] * 4) +
                ($cnpj[10] * 3) +
                ($cnpj[11] * 2);
        $resto = $soma1 % 11;
        $digito1 = $resto < 2 ? 0 : 11 - $resto;
        $soma2 = ($cnpj[0] * 6) +
                ($cnpj[1] * 5) +
                ($cnpj[2] * 4) +
                ($cnpj[3] * 3) +
                ($cnpj[4] * 2) +
                ($cnpj[5] * 9) +
                ($cnpj[6] * 8) +
                ($cnpj[7] * 7) +
                ($cnpj[8] * 6) +
                ($cnpj[9] * 5) +
                ($cnpj[10] * 4) +
                ($cnpj[11] * 3) +
                ($cnpj[12] * 2);
        $resto = $soma2 % 11;
        $digito2 = $resto < 2 ? 0 : 11 - $resto;
        return (($cnpj[12] == $digito1) && ($cnpj[13] == $digito2));
    }

}


?>
