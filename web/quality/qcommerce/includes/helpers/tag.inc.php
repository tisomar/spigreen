<?php

/**
 *
 * Transforma um array php para atributos HTML
 *
 * @param array $attributes Um array de atributos onde indice eh o nome do atributo e valor eh o valor do atributo
 * @return string Uma string com todos os atributos
 */
function get_atributes_html(array $atributes = array())
{
    $attri = '';
    foreach ($atributes as $attr => $value) {
        if ($attr == 'name' && (!isset($atributes['id']))) {// se nao foi passado um parametro id, crio ele com base no parametro nome
            $attri .= "id='" . get_id_from_name($value) . "' ";
        }
        $attri .= $attr . "='" . $value . "'";
    }

    return $attri;
}

/**
 *
 * Substitui caracteres nao reconhecidos por alguns browsers por _ na string.
 *
 * @param string $name O nome a ser traduzido para um elemento ID
 * @return string O nome traduzido
 */
function get_id_from_name($name)
{
    return str_replace(array('[', ']'), '_', $name);
}

/**
 *
 * Retorna um tag select
 *
 * @param array $values Um array que sera transformado em option da tag select
 * @param mixed $valueSelected o valor selecionado
 * @param array $atributes array para ser tranformado em atributos HTML
 * @see get_atributes_html()
 * @return string codigo html de uma tag select contendo array como options
 */
function get_form_select(array $values, $valueSelected, array $atributes = array())
{
    $ret = '<select ' . get_atributes_html($atributes) . ' >';
    foreach ($values as $key => $value) {
        $ret .= "<option value='$key' " . (((string) $key === (string) $valueSelected) ? "selected='selected'" : '') . ">$value</option>";
    }
    $ret .= '</select>';

    return $ret;
}

function get_form_select_clientes($value = null, array $atributes = array())
{
    $arrClientes = ClienteQuery::create()
        ->find();

    $ret = '<select ' . get_atributes_html($atributes) . ' >';
    $ret .= '<option value=""></option>';

    foreach ($arrClientes as $objCliente) {
        $ret .= '<option value="' . $objCliente->getId() . '" ' . (($value == $objCliente->getId()) ? "selected" : "") . '>' . $objCliente->getNomeCompleto() . '</option>';
    }

    $ret .= '</select>';

    return $ret;
}
/**
 *
 * Retorna uma tag select preenchida com os objetos passados como parametros
 *
 * @param array $objects Um array de objetos
 * @param mixed $value o valor do option selecionado
 * @param string $valueMethod nome metodo a ser chamado para preencher o valor do option do select
 * @param string $textMethod nome metodo a ser chamado para preencher o text do option do select
 * @param array $atributes array para ser tranformado em atributos HTML
 * @param array $addOptions array para ser mesclado com os do objeto
 * @return string tag select
 */
function get_form_select_object($objects, $value, $valueMethod, $textMethod, array $atributes = array(), $addOptions = array())
{
    $values = array();

    foreach ($addOptions as $key => $option) {
        $values[$key] = $option;
    }

    foreach ($objects as $object) {
        $values[htmlentities($object->$valueMethod())] = htmlentities($object->$textMethod());
    }

    if (count($addOptions) > 0) {
        // ksort($values);
    }

    return get_form_select($values, $value, $atributes);
}

/**
 *
 * Retorna uma tag html <img /> preparada para a criação de um thumb utilizando o arquivo de resize de imagens
 *
 * @param string $img Nome da imagem a ser redimensionada
 * @param integer $width Largura da nova imagem
 * @param integer $height Altura da nova imagem
 * @param string $atributes array de atributs html
 * @param string $pathImg Endereço que aponta para a pasta da imagem. Será adicionado na frente desse endereço a constante ROOT_PATH definida em include_propel.inc.php. Valor padrao: /arquivos/
 * @return string O html da tag <img>
 */
function get_img_thumb($img, $width, $height, $cropratio = false, $atributes = array(), $pathImg = '/arquivos/')
{
    $tagImg = '<img ' . get_atributes_html($atributes);

    $pathImg = ROOT_PATH . $pathImg; // Defino a path para img de acordo com o root dir da aplicxação. caso local seria /pryvijan/$pathImg. caso remoto seria apenas $pathImg

    $pathImgResize = ROOT_PATH . '/resize/image.php/';

    $tagImg .= " src='$pathImgResize$img?width=$width&height=$height&image=$pathImg$img" . ($cropratio ? '&cropratio=' . $cropratio : '') . "' />";

    return $tagImg;
}

/**
 *
 * @param string $texto O texto a ser resumido (cortado)
 * @param integer $qtdChars A quantidade de caracteres que o texto pode conter
 * @param string $sufix Um sufixo para ser adicionado ao final do texto default: '...'
 * @return string O texto resumido (cortado)
 */
function resumo($texto, $qtdChars, $sufix = '...')
{
    $texto = strip_tags($texto);

    if (strlen($texto) > $qtdChars) {
        $texto = substr($texto, 0, $qtdChars);

        $lastSpace = strripos($texto, ' ');

        return substr($texto, 0, $lastSpace) . $sufix;
    } else {
        return $texto;
    }
}

/**
 *
 * Retorna uma tag span de conteudo sim caso boolean seja true, ou tag span vermelha caso boolean seja false
 *
 * @param boolean $boolean O valor booleano
 * @return string
 */
function get_desc_boolean($boolean)
{
    $content = $boolean ? 'Sim' : 'Não';

    return get_span_color_boolean($boolean, $content);
}

/**
 *
 * * Retorna uma tag span de com a cor de acordo com o parametro boolean
 *
 * @param boolean $boolean
 * @param string $content O conteudo a ser adicionado dentro da tag span
 * @param array $arrColor Um array com as cores a serem usadas como padrao, indice 0 indica cor se caso for false o parametro boolean, indice 1 caso for true
 * @return string
 */
function get_span_color_boolean($boolean, $content, $arrColor = array(0 => 'red', 1 => 'green'))
{
    $color = $boolean ? $arrColor[1] : $arrColor[0];

    return "<span style='color:$color' >$content</span>";
}

function label($content, $class = 'default', $icon = null)
{
    if ($icon != null) {
        $icon = "<i class='{$icon}'></i> ";
    }
    return "<label class='label label-{$class}'>{$icon}{$content}</label>";
}

function get_toggle_option($object, $method, $pk, $defaultValue = 0)
{
    $defaultValue = $defaultValue ? 'on' : 'off';
    return sprintf('<div class="toggle %s" data-id="%s" data-method="%s" data-object="%s"></div>', $defaultValue, $pk, $method, $object);
}

/**
 *
 * Retorna um array com todos os estados do brasil onde chave eh estado com 2 caracteres e valor e o nome do estado
 *
 * @return array
 */
function get_estados()
{
    return array(
        'AC' => 'Acre',
        'AL' => 'Alagoas',
        'AM' => 'Amazonas',
        'AP' => 'Amapa',
        'BA' => 'Bahia',
        'CE' => 'Ceará',
        'DF' => 'Distrito Federal',
        'ES' => 'Espírito Santo',
        'GO' => 'Goiás',
        'MA' => 'Maranhão',
        'MS' => 'Mato Grosso do Sul',
        'MT' => 'Mato Grosso',
        'MG' => 'Minas Gerais',
        'PA' => 'Pará',
        'PB' => 'Paraíba',
        'PR' => 'Paraná',
        'PE' => 'Pernambuco',
        'PI' => 'Piauí',
        'RJ' => 'Rio de Janeiro',
        'RN' => 'Rio Grande do Norte',
        'RS' => 'Rio Grande do Sul',
        'RO' => 'Rondônia',
        'RR' => 'Roraima',
        'SC' => 'Santa Catarina',
        'SP' => 'São Paulo',
        'SE' => 'Sergipe',
        'TO' => 'Tocantins',
    );
}

// -----------------------------------------------------------------------------

function get_ordenacao_produto_options()
{
    return array(
        'mais-vendidos' => 'Mais vendidos',
        'melhor-avaliado' => 'Melhor avaliados',
        'preco-asc' => 'Menor preço',
        'preco-desc' => 'Maior preço',
        'nome-asc' => 'Nome (A-Z)',
        'nome-desc' => 'Nome (Z-A)',
    );
}

function get_select_filtro_ordenacao($defaultValue = null)
{
    $html = get_form_select(get_ordenacao_produto_options(), $defaultValue, array('name' => 'ordenar-por', 'class' => 'form-control input-sm'));

    return $html;
}

function get_exibicao_produto_options()
{
    return array(
        12 => 'Exibir 12 produtos',
        24 => 'Exibir 24 produtos',
        36 => 'Exibir 36 produtos',
        48 => 'Exibir 48 produtos',
    );
}

function get_select_filtro_exibicao($defaultValue = null)
{

    $html = get_form_select(get_exibicao_produto_options(), $defaultValue, array('name' => 'produtos-por-pagina', 'class' => 'form-control input-sm'));

    return $html;
}

function _renderGroupButtons()
{
    
    ob_start();
    foreach (func_get_args() as $argument) {
        $argument->render();
    }
    $content = ob_get_contents();
    ob_end_clean();

    $group = new PFBC\Element\HTML(
        '<div class="btn-group">' . $content . '</div>'
    );
                    
    $group->render();
}
