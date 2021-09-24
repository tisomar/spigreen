<?php
function encrypt($data, $key)
{
    return base64_encode(
        mcrypt_encrypt(
            MCRYPT_RIJNDAEL_128,
            $key,
            $data,
            MCRYPT_MODE_CBC,
            "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0"
        )
    );
}
function decrypt($data, $key)
{
    $decode = base64_decode($data);
    return mcrypt_decrypt(
        MCRYPT_RIJNDAEL_128,
        $key,
        $decode,
        MCRYPT_MODE_CBC,
        "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0"
    );
}

function redirect_404admin()
{
    redirectTo(get_url_admin() . '/404');
    exit;
}

function toUtf8(&$item, $key)
{
    $item = iconv("iso-8859-1", "utf-8", $item);
}

function formataCpf($cpf)
{

    $cpf = substr($cpf, 0, 3) . "." . substr($cpf, 3, 3) . "." . substr($cpf, 6, 3) . "-" . substr($cpf, 9, 2);

    return $cpf;
}

function trata_post_array(array $arr)
{
    if (is_array($arr)) {
        $arr = @array_map('trim', $arr);
    }
    return $arr;
}

function redirect_listagem($alternativo)
{
    $location = '';

    if (!empty($_SESSION['ULTIMA_LISTAGEM'])) {
        $location = $_SESSION['ULTIMA_LISTAGEM'];
    } else {
        $location = $alternativo;
    }

    redirectTo($location);
    exit;
}

function get_filtro_form_select(array $values, $valueSelected, array $atributes = array())
{

    if (!isset($values[''])) {
        $values = array_merge(array('' => 'Todos'), $values);
    }

    return get_form_select($values, $valueSelected, $atributes);
}

function get_filtro_form_select_booleano($valueSelected, array $atributes = array())
{
    $values = array();

    $values['S'] = 'Sim';
    $values['N'] = 'Não';

    return get_filtro_form_select($values, $valueSelected, $atributes);
}

/**
 * Convert a shorthand byte value from a PHP configuration directive to an integer value
 * @param    string   $value
 * @return   int
 */
function convertBytes($value)
{
    if (is_numeric($value)) {
        return $value;
    } else {
        $value_length = strlen($value);
        $qty = substr($value, 0, $value_length - 1);
        $unit = strtolower(substr($value, $value_length - 1));
        switch ($unit) {
            case 'k':
                $qty *= 1024;
                break;
            case 'm':
                $qty *= 1048576;
                break;
            case 'g':
                $qty *= 1073741824;
                break;
        }
        return $qty;
    }
}

function createUrl($url)
{
    return ROOT_PATH . $url;
}

function edit_inline($default, $model, $method, $pk, $type = "text", $attr = array())
{
    $url = ROOT_PATH . '/admin/ajax/save-data/?model=' . $model . '&method=' . $method;
    if (isset($attr['data-applymask']) && $attr['data-applymask'] == 'maskMoney') {
        $url .= '&format_number=true';
    }
    
    $attr = get_atributes_html($attr);
    
    $tag = '<a ' . $attr . ' href="#" class="editable" data-placement="top" data-pk="%s" data-type="%s" data-url="%s">%s</a>';

    return sprintf($tag, $pk, $type, $url, $default);
}

function _dica($dica)
{
    $html = '
        <div class="ui-widget">
            <div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: .7em;">
                <p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
                    <strong>Dica:</strong> %s</p>
            </div>
        </div>
    ';

    return sprintf($html, $dica);
}

function array_merge_recursive_distinct(array &$array1, array &$array2)
{
    $merged = $array1;

    foreach ($array2 as $key => &$value) {
        if (is_array($value) && isset($merged [$key]) && is_array($merged [$key])) {
            $merged [$key] = array_merge_recursive_distinct($merged [$key], $value);
        } else {
            $merged [$key] = $value;
        }
    }

    return $merged;
}

function formatBytes($size, $precision = 2)
{
    $base = log($size) / log(1024);
    $suffixes = array('', 'K', 'M', 'G', 'T');

    return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
}

function delete($class, $id)
{
    $base_url_delete = ROOT_PATH . '/admin/actions/delete';

    parse_str($_SERVER['QUERY_STRING'], $param);

    $param = array_merge($param, array(
        'class' => $class,
        'id' => $id,
    ));

    return $base_url_delete . '?' . http_build_query($param);
}

/**
 * Retorna todas as combinações possíveis de um array multidimensional
 * @param array $data Matriz multidimensional contendo os dados
 * @param string $separator String que será utilizada para separar os elementos
 * @return array Uma matriz contendo a lista de combinações
 */
function array_combine_variacoes(array $data, $separator = ',')
{
    $response = new stdClass();
    $callback = function ($item, $key, $aux) use ($response, $separator) {
        $aux[2][] = $item;

        if (count($aux[0])) {
            $fisrtElement = array_shift($aux[0]);
            array_walk($fisrtElement, $aux[1], array($aux[0], $aux[1], $aux[2]));
        } else {
            $response->data[] = implode($separator, $aux[2]);
        }
    };

    $response->data = array();

    $firstElement = array_shift($data);
    array_walk($firstElement, $callback, array($data, $callback, array()));

    return $response->data;
}

//function checkPath($name) {
//    $p = new Pagina();
//    $targetDir = $_SERVER['DOCUMENT_ROOT'] . BASE_PATH . $p->strPathImg . $name;
//    if (!is_dir($targetDir)) {
//        mkdir($targetDir, 0777, true);
//        chmod($targetDir, 0777);
//    }
//    return true;
//}
