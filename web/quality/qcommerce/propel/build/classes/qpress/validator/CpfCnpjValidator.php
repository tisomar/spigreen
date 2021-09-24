<?php

class CpfCnpjValidator implements BasicValidator
{

    private $arrCpfInvalidos = array(
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
    private $arrCnpjInvalidos = array(
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

    public function isValid(ValidatorMap $map, $str)
    {
        // Primeiramente deixa somente a numeração para que a validação ocorra
        $document_number = preg_replace('/[^0-9]/', "", $str);

        // Verifica se o CPF ou CNPJ digitado está no array de inválidos conhecidos
        if (in_array($document_number, $this->arrCpfInvalidos) || in_array($document_number, $this->arrCnpjInvalidos)) {
            return false;
        }

        // Verifica quantidade de caracteres, sendo 11 retorna validação de CPF
        if (strlen($document_number) === 11) {
            $map->setMessage('O CPF digitado &eacute inv&aacute;lido.');
            return $this->isValidCpf($document_number);
        }
        // Caso seja 14, verifica se é um CNPJ válido
        if (strlen($document_number) === 14) {
            $map->setMessage('O CNPJ digitado &eacute inv&aacute;lido.');
            return $this->isValidCnpj($document_number);
        }

        $map->setMessage('O CPF ou CNPJ digitado &eacute inv&aacute;lido.');

        // Caso chegue até aqui, é porque o documento digitado é inválido
        return false;
    }

    /**
     * Verifica a validade do cpf
     * 
     * @param type $cpf
     * @return boolean 
     */
    protected function isValidCpf($cpf)
    {
        $arrCPF = array();
        for ($i = 0; $i < 11; $i++) {
            $arrCPF[$i] = (int) $cpf[$i];
        }
        $arrDV = array(0, 0);

        for ($i = 10; $i >= 2; $i--) {
            $arrDV[0] += ($i * ($arrCPF[10 - $i]));
        }
        $arrDV[0] = (11 - ($arrDV[0] % 11));
        if ($arrDV[0] >= 10) {
            $arrDV[0] = 0;
        }

        for ($i = 11; $i >= 2; $i--) {
            $arrDV[1] += ($i * ($arrCPF[11 - $i]));
        }
        $arrDV[1] = (11 - ($arrDV[1] % 11));
        if ($arrDV[1] >= 10) {
            $arrDV[1] = 0;
        }

        return (($arrCPF[9] == $arrDV[0]) && ($arrCPF[10] == $arrDV[1]));
    }

    /**
     * Verifica a validade do cnpj
     * 
     * @param type $cnpj
     * @return boolean 
     */
    protected function isValidCnpj($cnpj)
    {
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
