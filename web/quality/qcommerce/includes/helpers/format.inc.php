<?php

/**
 *
 * Limpa um cep, retira o digito '-' e substitui por string vazia
 *
 * @param string $cep
 * @return string
 */
function clear_cep($cep)
{
    return str_replace('-', '', $cep);
}

/**
 *
 * Formata um cep, retira o digito '-' e substitui por string vazia
 *
 * @param string $cep
 * @return string
 */
function format_cep($cep)
{
    if ($cep !== '') {
        $cep = str_pad($cep, 8, '0', STR_PAD_LEFT);
        $cep = substr($cep, 0, 5) . '-' . substr($cep, 5, 3);
    }
    return $cep;
}

/**
 * Retira caracteres do cpf para salvar no banco de dados
 *
 * @param string $cpf
 * @return string
 */
function clear_cpf($cpf)
{
    return str_replace(array('.', '-'), '', $cpf);
}

/**
 *
 * Formta um cpf que veio do banco de dados para apresentação
 *
 * @param string $cpf
 * @return string
 */
function format_cpf($cpf)
{
    return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
}

function format_number($number, $type = UsuarioPeer::LINGUAGEM_PORTUGUES, $decimals = 2)
{
    if ($type == UsuarioPeer::LINGUAGEM_INGLES) {
        $number = str_replace('.', '', $number);
        $number = str_replace(',', '.', $number);
        $number = (double) $number;
    }
    switch ($type) {
        case UsuarioPeer::LINGUAGEM_PORTUGUES:
            return number_format($number, $decimals, ',', '.');
            break;
        case UsuarioPeer::LINGUAGEM_INGLES:
            return number_format($number, $decimals, '.', '');
            break;

        default:
            return number_format($number, ',', '.');
            break;
    }
}

function format_money($v, $prepend = '')
{
    return $prepend . format_number($v);
}


/**
 *
 * @param float $pontos
 * @return string
 */
function formata_pontos($pontos)
{
    return number_format($pontos, 2, ',', '.');
}

function format_data($data, $type)
{
    switch ($type) {
        case UsuarioPeer::LINGUAGEM_INGLES :
            {
                $partes = explode('/', $data);
                $retorno = $partes[2] . '-' . $partes[1] . '-' . $partes[0];
        } break;

        case UsuarioPeer::LINGUAGEM_PORTUGUES :
            {
                $partes = explode('-', $data);
                $retorno = $partes[0] . '-' . $partes[1] . '-' . $partes[2];
        } break;
    }

    return $retorno;
}

/**
 *
 * Retorna string da linguagem de acrodo com parametro
 *
 * @param string $linguagem
 * @return string
 */
function get_desc_linguagem($linguagem)
{
    switch ($linguagem) {
        case UsuarioPeer::LINGUAGEM_PORTUGUES:
            return 'Português';
            break;
        case UsuarioPeer::LINGUAGEM_INGLES:
            return 'Inglês';
            break;
        case UsuarioPeer::LINGUAGEM_ESPANHOL:
            return 'Espanhol';
            break;

        default:
            return '';
            break;
    }
}

/**
 *
 * Pega id do video do youtube na url
 *
 * @param string $url
 * @return string
 */
function format_url_youtube($url)
{
    $arrIdVideo = spliti("[\?&]v=", $url);
    //somente apos o paremetro
    $arrIdVideo = $arrIdVideo[1];

    //retirando '&'s
    $arrIdVideo = explode("&", $arrIdVideo);

    //isolando somente o id
    $strIdVideo = $arrIdVideo[0];

    return $strIdVideo;
}

/**
 *
 * Retorna o nome do mes em extenso
 *
 * @param integer $mes O numero do mes
 * @return string Uma string com o nome do mes
 */
function get_mes_extenso($mes)
{
    switch ($mes) {
        case 1:
            $mes = 'Janeiro';
            break;
        case 2:
            $mes = 'Fevereiro';
            break;
        case 3:
            $mes = 'Março';
            break;
        case 4:
            $mes = 'Abril';
            break;
        case 5:
            $mes = 'Maio';
            break;
        case 6:
            $mes = 'Junho';
            break;
        case 7:
            $mes = 'Julho';
            break;
        case 8:
            $mes = 'Agosto';
            break;
        case 9:
            $mes = 'Setembro';
            break;
        case 10:
            $mes = 'Outubro';
            break;
        case 11:
            $mes = 'Novembro';
            break;
        case 12:
            $mes = 'Dezembro';
            break;
        default:
            $mes = '';
            break;
    }

    return $mes;
}

function get_array_mes()
{
    return array(
        '1' => 'Janeiro',
        '2' => 'Fevereiro',
        '3' => 'Março',
        '4' => 'Abril',
        '5' => 'Maio',
        '6' => 'Junho',
        '7' => 'Julho',
        '8' => 'Agosto',
        '9' => 'Setembro',
        '10' => 'Outubro',
        '11' => 'Novembro',
        '12' => 'Dezembro',
    );
}

function get_array_anos()
{
    $optAno = array();
    $ano = intval(date('Y', strtotime('-2 years')));
    for($q=$ano;$q<=($ano+20);$q++):
        $optAno[$q] = $q;
    endfor;

    return $optAno;
}

function get_array_mes_abreviado($indiceJaneiro = 1)
{
    return array($indiceJaneiro => 'Jan', 'Fev', 'Mar', 'Abr', 'Maio', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez');
}

function get_mes_abreviado($mes, $indiceJaneiro = 1)
{
    $meses = get_array_mes_abreviado($indiceJaneiro);
    if (array_key_exists($mes, $meses)) {
        return $meses[$mes];
    } else {
        return '';
    }
}

function get_dia_semana($day)
{
    $days = array(
        'Domingo',
        'Segunda-feira',
        'Terça-feira',
        'Quarta-feira',
        'Quinta-feira',
        'Sexta-feira',
        'Sábado',
    );
    
    if (isset($days[$day])) {
        return $days[$day];
    }
    
    return '';
}

/**
 *
 * Retorna se um email e valido ou nao
 *
 * @param string $email O email a ser validado
 * @return boolean True se for email valido false senao
 */
function isValidEmail($email)
{
    return preg_match("/\A[A-Za-z0-9\\._-]+@[A-Za-z]+\.[A-Za-z.]+\Z/", $email);
}

/**
 * Converte o nome de um campo do formulário em um nome legível para humanos
 * Ex.: "seu_email" será convertido em: "Seu email"
 *
 * @param string $nome Nome que deseja-se converter
 * @return string Nome convertido
 */
function convert_nomeform_to_nomenormal($nome)
{
    $nome = ucfirst($nome);
    $novoNome = '';

    $arrNome = explode('_', $nome);

    foreach ($arrNome as $itemNome) {
        $novoNome .= $itemNome . ' ';
    }

    return (count($arrNome) > 1) ? trim($novoNome) : $nome;
}

/**
 * Converte gramas em quilos
 *
 * @param int $gramas
 * @return string
 */
function converte_gramas_em_quilos($gramas)
{
    $gramas = (int) $gramas;

    return format_number($gramas / 1000, \UsuarioPeer::LINGUAGEM_PORTUGUES);
}

/**
 * Limpa todas as tags HTML e PHP, além de remover as quebras
 * de linha, espaçamentos, etc.
 *
 * Ex.:
 * <code>
 * $text = '<p>Test paragraph.</p><!-- Comment -->
 * <a href="#fragment">Other text</a>';
 * </code>
 *
 * Retorno: Test paragraph. Other text
 *
 * @author Felipe Corrêa
 * @since 2013-06-04
 * @param string $html Conteúdo que deseja-se limpar
 * @return string String limpa com texto puro
 */
function limpar_html($html)
{
    // Removendo todas as tags html e php do texto
    $html = strip_tags($html);
    // Removendo \n \t \r (quebras de linha, tabulações)
    $html = preg_replace('/(\v|\s)+/', ' ', $html);
    // Convertendo caracteres como &atilde para á (para não consumir espaço na contagem)
    $html = html_entity_decode($html, ENT_COMPAT, 'UTF-8');

    return $html;
}

/**
 * Remove palavras comuns e não relevantes para um motor de busca
 *
 * @author Felipe Corrêa
 * @since 2013-06-04
 * @param String|array $texto Texto que deseja-se limpar as palavras sem relevância
 * @return String Retorna o texto inicial com as palavras existentes no
 *                $arrRemover removidas
 */
function remover_palavras_comuns($texto)
{
    $arrRemover = array('a', 'o', 'e', 'de', 'do', 'dos', 'da', 'das', 'na', 'nas', 'no', 'nos',
        'como', 'para', 'diz', 'em', 'por', 'pelo', 'pela', 'pode', 'ou', 'é',
        'ao', 'tem', 'deve', 'após');

    $arrLimpo = array();

    // Se o texto não é um array, então converte-o para um quebrando pelo espaço
    if (!is_array($texto)) {
        $texto = explode(' ', $texto);
    }

    foreach ($texto as $palavra) {
        // Se a palavra iterada estiver no $arrRemover, então não será inserida
        // no array de retorno ($arrLimpo)
        if (!in_array($palavra, $arrRemover)) {
            $arrLimpo[] = $palavra;
        }
    }

    return implode(' ', $arrLimpo);
}
