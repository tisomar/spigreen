<?php

/**
 * Remove todos os caracteres que não são digitos da string.
 *
 * @param $string
 * @return string|string[]|null
 */
function only_digits($string)
{
    return preg_replace('/[^0-9]/', "", $string);
}

function isset_or_null(&$v)
{
    return isset_or($v, null);
}

function isset_or(&$v, $a = '')
{
    return ((isset($v)) ? $v : $a);
}

function is_empty($valor)
{
    $valor = trim($valor);

    return (empty($valor));
}

/**
 * Retorna o ultimo dia de um mês.
 *
 * @param mixed $mes Inteiro indicando o mês ou null para o mês atual.
 * @param mixed $ano Inteiro indicando o ano ou null para o ano atual.
 *
 * @return int Ultimo dia do mes.
 */
function get_ultimo_dia_mes($mes = null, $ano = null)
{
    if ($mes === null) {
        $mes = date('m');
    }
    if ($ano === null) {
        $ano = date('Y');
    }
    return (int) date('t', mktime(0, 0, 0, $mes, 1, $ano));
}

function get_datas_limite_mes($mes = null, $ano = null)
{
    if (is_null($mes) || is_null($ano)) {
        $mes = date('m');
        $ano = date('Y');
    }

    $mkt_data_ini = mktime(0, 0, 0, $mes, 1, $ano);
    $mkt_data_fim = mktime(0, 0, 0, $mes, get_ultimo_dia_mes($mes, $ano), $ano);

    $data_ini = date('Y-m-d', $mkt_data_ini);
    $data_fim = date('Y-m-d', $mkt_data_fim);

    return array($data_ini, $data_fim);
}


function formata_cnpj($v)
{
    $v = only_digits($v);

    $ret = substr($v, 0, 2);
    $sub = substr($v, 2, 3);
    if ($sub) {
        $ret .= '.' . $sub;

        $sub = substr($v, 5, 3);
        if ($sub) {
            $ret .= '.' . $sub;

            $sub = substr($v, 8, 4);
            if ($sub) {
                $ret .= '/' . $sub;

                $sub = substr($v, 12, 2);
                if ($sub) {
                    $ret .= '-' . $sub;
                }
            }
        }
    }

    return $ret;
}

function formata_cpf($v)
{
    $v = only_digits($v);

    $ret = substr($v, 0, 3);
    $sub = substr($v, 3, 3);
    if ($sub) {
        $ret .= '.' . $sub;

        $sub = substr($v, 6, 3);
        if ($sub) {
            $ret .= '.' . $sub;

            $sub = substr($v, 9, 2);
            if ($sub) {
                $ret .= '-' . $sub;
            }
        }
    }

    return $ret;
}

function formata_cep($v)
{
    $v = only_digits($v);
    if (((int) $v) == 0) {
        return '';
    }

    $ret = substr($v, 0, 5);
    $sub = substr($v, 5, 3);
    if ($sub) {
        $ret .= '-' . $sub;
    }
    return $ret;
}

/**
 * Valida quantidade de caracteres de um CEP e se são apenas números
 * além de verificar se não é um cep 00000000 ou 99999999
 *
 * @param string $cep Cep no formato 99999-999 ou 99999999
 * @return boolean Retorna true se o CEP for válido
 */
function valida_cep($cep)
{
    $cep = clear_cep($cep);

    if ($cep == '00000000' || $cep == '99999999') {
        return false;
    } elseif (strlen($cep) == 8 && is_numeric($cep)) {
        return true;
    }

    return false;
}

function valida_data($strData)
{
    $arrPartes = preg_split('![/-]!', $strData);
    return (count($arrPartes) == 3 && checkdate($arrPartes[1], $arrPartes[0], $arrPartes[2]));
}

/**
 * Verifica se uma URL é valida e se é uma URL deste domínio
 *
 * @param String $url URL que deseja-se verificar
 * @return boolean Retorna true na caso da URL ser válida
 */
function valida_url_redirecionamento($url)
{
    $urlDominio = $_SERVER['SERVER_NAME'] . ROOT_PATH;

    // Verificando se é uma URL válida
    if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
        // Verificando se é uma URL deste domínio
        if (strpos($url, $urlDominio) !== false) {
            return true;
        }
    }

    return false;
}

function valida_horario($strHorario)
{
    $ret = false;

    $arrPartes = explode(':', $strHorario);
    if (count($arrPartes) == 2 && is_numeric($arrPartes[0]) && is_numeric($arrPartes[1])) {
        $intHora = (int) $arrPartes[0];
        $intMinuto = (int) $arrPartes[1];
        $ret = ($intHora >= 0 && $intHora < 24) && ($intMinuto >= 0 && $intMinuto < 60);
    }

    return $ret;
}

function data_mysql($strData, $boolValidar = true)
{
    $strRet = '';
    $arrPartes = preg_split('![/-]!', $strData);
    if (count($arrPartes) == 3) {
        if (!$boolValidar || checkdate($arrPartes[1], $arrPartes[0], $arrPartes[2])) {
            $strRet = sprintf('%s-%s-%s', $arrPartes[2], $arrPartes[1], $arrPartes[0]);
        }
    }
    return $strRet;
}

function data_mysqlAsTimestamp($strData, $boolValidar = true)
{
    $strData = data_mysql($strData, $boolValidar);
    if ($strData) {
        $arrPartes = explode('-', $strData);
        if (count($arrPartes) == 3) {
            return mktime(0, 0, 0, $arrPartes[1], $arrPartes[2], $arrPartes[0]);
        }
    }
    return false;
}

function substituiCaracteresAcentuados($str)
{
    $str = preg_replace('/[áàãâä]/', 'a', $str);
    $str = preg_replace('/[ÁÀÃÂÄ]/', 'A', $str);
    $str = preg_replace('/[éèêë]/', 'e', $str);
    $str = preg_replace('/[ÉÈÊË]/', 'E', $str);
    $str = preg_replace('/[íìîï]/', 'i', $str);
    $str = preg_replace('/[ÍÌÎÏ]/', 'I', $str);
    $str = preg_replace('/[óòõÔÖ]/', 'o', $str);
    $str = preg_replace('/[ÓÒÕÔÖ]/', 'O', $str);
    $str = preg_replace('/[úùûü]/', 'u', $str);
    $str = preg_replace('/[ÚÙÛÜ]/', 'U', $str);
    return $str;
}

function valorPorExtenso($valor = 0)
{
    $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
    $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões", "quatrilhões");

    $rt = '';

    $c = array("", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
    $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa");
    $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezesete", "dezoito", "dezenove");
    $u = array("", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove");

    $z = 0;

    $valor = number_format($valor, 2, ".", ".");
    $inteiro = explode(".", $valor);
    for ($i = 0; $i < count($inteiro); $i++) {
        for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++) {
            $inteiro[$i] = "0" . $inteiro[$i];
        }
    }

    // $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
    $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
    for ($i = 0; $i < count($inteiro); $i++) {
        $valor = $inteiro[$i];
        $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
        $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
        $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

        $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
        $t = count($inteiro) - 1 - $i;
        $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
        if ($valor == "000") {
            $z++;
        } elseif ($z > 0) {
            $z--;
        }
        if (($t == 1) && ($z > 0) && ($inteiro[0] > 0)) {
            $r .= ( ($z > 1) ? " de " : "") . $plural[$t];
        }
        if ($r) {
            $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
        }
    }

    return($rt ? $rt : "zero");
}

/**
 * Calculates a date lying a given number of months in the future of a given date.
 * The results resemble the logic used in MySQL where '2009-01-31 +1 month' is '2009-02-28' rather than '2009-03-03' (like in PHP's strtotime).
 *
 * @author akniep
 * @since 2009-02-03
 * @param $base_time long, The timestamp used to calculate the returned value .
 * @param $months int, The number of months to jump to the future of the given $base_time.
 * @return long, The timestamp of the day $months months in the future of $base_time
 */
function get_x_months_to_the_future($base_time = null, $months = 1)
{
    if (is_null($base_time)) {
        $base_time = time();
    }

    $x_months_to_the_future = strtotime("+" . $months . " months", $base_time);

    $month_before = (int) date("m", $base_time) + 12 * (int) date("Y", $base_time);
    $month_after = (int) date("m", $x_months_to_the_future) + 12 * (int) date("Y", $x_months_to_the_future);

    if ($month_after > $months + $month_before) {
        $x_months_to_the_future = strtotime(date("Ym01His", $x_months_to_the_future) . " -1 day");
    }

    return $x_months_to_the_future;
}

function valida_email($str)
{
    return filter_var($str, FILTER_VALIDATE_EMAIL) != false;
}

function escape($str)
{
    return htmlspecialchars($str, ENT_NOQUOTES, 'UTF-8');
}

function simple_curl($url, $post = array(), $get = array())
{
    $url = explode('?', $url, 2);
    if (count($url) === 2) {
        $temp_get = array();
        parse_str($url[1], $temp_get);
        $get = array_merge($get, $temp_get);
    }
    $ch = curl_init($url[0] . "?" . http_build_query($get));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $response = curl_exec($ch);

    return $response;
}

function isValidCnpj($cnpj)
{
    $arrInvalidos = array(
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

    $cnpj = preg_replace('/[^0-9]/', "", $cnpj);

    if (strlen($cnpj) <> 14) {
        return false;
    }

    foreach ($arrInvalidos as $strInvalido) {
        if ($strInvalido == $cnpj) {
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

function isValidCpf($cpf)
{
    $cpf = preg_replace('/[^0-9]/', "", $cpf);

    $arrInvalidos = array(
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

    foreach ($arrInvalidos as $strInvalido) {
        if ($strInvalido == $cpf) {
            return false;
        }
    }

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

function isValidDate($date)
{
    if (preg_match("/^(0[1-9]|[12][0-9]|3[01])[/ /.](0[1-9]|1[012])[/ /.](19|20)\d\d$/", $date)) {
        return true;
    }
    return false;
}

/**
 * Retorna todas as combinações possíveis de um array multidimensional
 * @param array $data Matriz multidimensional contendo os dados
 * @return array Uma matriz contendo a lista de combinações
 */
function array_combine_recursive(array $data)
{
    $response = new stdClass();

    $callback = function ($item, $key, $aux) use ($response) {
        $aux[2][$key] = $item;

        if (count($aux[0])) {
            array_walk(array_shift($aux[0]), $aux[1], array($aux[0], $aux[1], $aux[2]));
        } else {
            $response->data[]['opcoes'] = $aux[2];
        }
    };

    $response->data = array();

    array_walk(array_shift($data), $callback, array($data, $callback, array()));

    return $response->data;
}

/**
 * retorna o conteudo de um include, encapsulando as variaveis necessárias para
 * sua execução
 *
 * @param $_filename
 * @param $_args
 * @return string
 * @throws Excepetion se o arquivo solicitado não existir
 */
function get_contents($_filename, $_args = array())
{
    if (is_file($_filename)) {
        if (ob_start()) {
            extract($_args, EXTR_PREFIX_INVALID, 'arg');
            include $_filename;
            $_contents = ob_get_contents();
            ob_end_clean();
            return $_contents;
        } else {
            throw new Exception('Não foi possível iniciar o buffer.');
        }
    } else {
        throw new Exception(sprintf('Não foi possível localizar o arquivo "%s"', $_filename));
    }
}

if (!function_exists('array_to_attr')) {

    function array_to_attr($attr)
    {
        $attr_str = '';

        foreach ((array) $attr as $property => $value) {
            if ($value === null or $value === false) {
                continue;
            }

            if (is_numeric($property)) {
                $property = $value;
            }

            $attr_str .= $property . '="' . $value . '" ';
        }

        return trim($attr_str);
    }

}

function add_scheme($url, $scheme = 'http://')
{
    return parse_url($url, PHP_URL_SCHEME) === null ?
            $scheme . $url : $url;
}

function get_select_all()
{
    return array(
        '' => 'Todos...'
    );
}

function get_select_default()
{
    return array(
        '' => 'Selecione...'
    );
}

function sprintf2($str = '', $vars = array(), $char = '%')
{
    if (!$str) {
        return '';
    }
    if (count($vars) > 0) {
        foreach ($vars as $k => $v) {
            $str = str_replace($char . $k, $v, $str);
        }
    }

    return $str;
}

function escape_post($data)
{
    if (is_string($data)) {
        return trim(stripslashes(htmlspecialchars($data)));
    }
    if (is_array($data)) {
        return array_map('escape_post', $data);
    }
    return $data;
}

function plural($num, $singular, $plural)
{
    if ($num > 1) {
        return sprintf($plural, $num);
    }
    return sprintf($singular, $num);
}

function aplicarPercentualDesconto($valor, $percentual = 0)
{
    return $valor * (1 - ($percentual / 100));
}

function aplicarPercentualAcrescimo($valor, $percentual = 0)
{
    return $valor * (1 + ($percentual / 100));
}

// ----------------------------------------------------------------------------
