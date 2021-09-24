<?php

/**
 * Retorna TRUE se o site recebeu um redirecionamento do mesmo servidor.
 * @return bool
 */
function verifyReferer($method = null)
{
    return is_null($method) ? true : (is_array($method) ? in_array($_SERVER['REQUEST_METHOD'], array_map('strtoupper', $method)) : $_SERVER['REQUEST_METHOD'] == strtoupper($method)) && isset($_SERVER['HTTP_REFERER']) && stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']);
}


function console($data)
{
    if (is_array($data) || is_object($data)) {
        echo("<script>console.log('" . json_encode($data) . "');</script>");
    } else {
        echo("<script>console.log('" . $data . "');</script>");
    }
}

function isProxy()
{
    return true;
}

function isLocalhost()
{
    return (in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1',)));
}


function isAjax()
{
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}

function redirect_404()
{
    redirectTo(ROOT_PATH . '/pagina-nao-encontrada');
    exit;
}

function exit_403()
{
    header("HTTP/1.1 403 Forbidden");
    exit;
}

function redirect_home()
{
    header('HTTP/1.1 200 OK');
    redirectTo(ROOT_PATH . '/');
    exit;
}

function redirect($caminho_interno)
{
    header('HTTP/1.1 200 OK');
    redirectTo(ROOT_PATH . $caminho_interno);
    exit;
}

function redirect_referer($alternativo = '')
{
    if (!empty($_SERVER['HTTP_REFERER'])) {
        redirectTo($_SERVER['HTTP_REFERER']);
    } else {
        if (!empty($alternativo)) {
            redirectTo($alternativo);
        } else {
            redirect_home();
        }
    }
    exit;
}

function url_para_noticia($noticia)
{
    $slug = slugify($noticia->getKey());
    if (!$slug) {
        $slug = slugify($noticia->getNome());
        if (!$slug) {
            $slug = 'novidade-' . $noticia->getId();
        }
    }
    return ROOT_PATH . '/novidades/detalhes/' . $slug . '/' . $noticia->getId();
}

function slugify($text)
{
    $text = utf8_encode($text);

    // replace non letter or digits by -
    $text = preg_replace('#[^\\pL\d]+#u', '-', $text);

    // trim
    $text = trim($text, '-');

    // transliterate
    if (function_exists('iconv')) {
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    }

    // lowercase
    $text = strtolower($text);

    // remove unwanted characters
    $text = preg_replace('#[^-\w]+#', '', $text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

function get_page_arg($argKey = 0)
{
    global $args;

    $page = 0;
    if (!empty($args[$argKey])) {
        $page = (int) $args[$argKey];
    }
    if ($page < 1) {
        $page = 1;
    }
    return $page;
}

function str_to_float($str)
{
    $str = str_replace('.', '', $str);
    $str = str_replace(',', '.', $str);
    return (float) $str;
}

function formata_valor($valor, $decimals = 2)
{
    return number_format($valor, $decimals, ',', '.');
}

/**
 * Remove as tags html de uma string.
 *
 * @param String $str Conteúdo html
 * @param String $allowable_tags Tags permitidas
 * @return String
 */
function removeTags($str, $allowable_tags = '')
{
    return strip_tags($str, $allowable_tags);
}

/**
 * Retorna o $_SERVER['REQUEST_URI'] sem o ROOT_PATH <br />
 *
 * Ex.: http://localhost/qcommerce.com.br/web/produtos/components/detalhes_produto/produto_calcula_frete/?idProduto=10 <br />
 * Retorna /produtos/components/detalhes_produto/produto_calcula_frete/?idProduto=10 se o parâmetro $removeRootPath = true <br />
 * Caso contário retorna /qcommerce.com.br/web/produtos/components/detalhes_produto/produto_calcula_frete/?idProduto=10
 *
 * @author Felipe Corrêa
 * @since 2013-03-13
 *
 * @param String $uri Caso deseje-se enviar um $url, Por padrão pega o $_SERVER['REQUEST_URI']
 * @param bool $removeRootPath Se deseja-se remover o ROOT_PATH do resultado (Default = true)
 * @return string
 */
function get_request_uri($uri = null, $removeRootPath = true)
{
    if (null === $uri) {
        $uri = $_SERVER['REQUEST_URI'];
    }

    if (!empty($uri)) {
        if ($removeRootPath == true) {
            $uri = str_replace(ROOT_PATH, '', $_SERVER['REQUEST_URI']);
        } else {
            $uri = $_SERVER['REQUEST_URI'];
        }

        return $uri;
    }

    return '';
}

/**
 * Retorna a base da URL com ou sem o ROOT_PATH
 *
 * Ex.: <br />
 * $url = http://localhost/qcommerce.com.br/web/produtos/components/detalhes_produto/produto_calcula_frete/?idProduto=10 <br />
 * Retorna /produtos/components/detalhes_produto/produto_calcula_frete/ se o parâmetro $removeRootPath = true <br />
 * Caso contário retorna /qcommerce.com.br/web/produtos/components/detalhes_produto/produto_calcula_frete/
 *
 * @author Felipe Corrêa
 * @since 2013-03-13
 *
 * @param String $url URL que deseja-se pegar a base (se for igual a NULL então pega a URL atual)
 * @param bool $removeRootPath Se deseja-se remover o ROOT_PATH do resultado (Default = true)
 *
 * @return String
 */
function get_url_caminho($url = null, $removeRootPath = true)
{
    if (null === $url) {
        $url = $_SERVER['REQUEST_URI'];
    }

    $arrUrl = parse_url($url);

    if (array_key_exists('path', $arrUrl)) {
        if ($removeRootPath == true) {
            $arrUrl['path'] = str_replace(ROOT_PATH, '', $arrUrl['path']);
        }

        return $arrUrl['path'];
    }

    return '';
}

/**
 * Retorna true caso seja SSL
 * @return boolean
 */
function is_ssl()
{
    if (isset($_SERVER['HTTPS'])) {
        if ('on' == strtolower($_SERVER['HTTPS'])) {
            return true;
        }
        if ('1' == $_SERVER['HTTPS']) {
            return true;
        }
    } elseif (isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] )) {
        return true;
    } elseif (isset($_SERVER['HTTP_X_SCHEME']) && ( 'https' == $_SERVER['HTTP_X_SCHEME'])) {
        return true;
    }

    return false;
}

/**
 * Retorna a URL completa do site já com o ROOT_PATH
 *
 * Exemplo de retorno: http://localhost/qcommerce.com.br/web
 *
 * @return String
 */
function get_url_site()
{
    $scheme = (is_ssl() ? 'https://' : 'http://');
    return $scheme . $_SERVER['SERVER_NAME'] . ROOT_PATH;
}

function get_url_admin()
{
    return get_url_site() . '/admin';
}

function redirectTo($url, $response_code = 302)
{

    global $container;

    if (parse_url($url, PHP_URL_SCHEME) === null) {
        $url = str_replace($container->getRequest()->getBaseUrl(), '', $url);
        $url = get_url_site() . $url;
    }

    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Pragma: no-cache');
    header('Location: ' . $url, true, $response_code);
    exit(0);
}

/**
 * Retorna a url de acesso a página com o iframe do google maps.
 *
 * @return string
 */
function get_url_google_maps()
{
    return get_url_site() . '/contato/mapa/?iframe=true&amp;width=650&amp;height=450';
}

function str_pad_days($input)
{
    return str_pad($input, 2, '0', STR_PAD_LEFT);
}

function get_days()
{
    $days = array_map('str_pad_days', range(1, 31));
    return array('' => 'Dia') + array_combine($days, $days);
}

function get_months()
{
    return array('' => 'Selecionar') + get_array_mes();
}

function get_months_abbreviation()
{
    return array('' => 'Mês') + get_array_mes_abreviado();
}

function get_loading_image($atributes = array())
{
    $src = "data:image/gif;base64,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";
    $attr = get_atributes_html($atributes);
    return sprintf('<img src="%s" %s />', $src, $attr);
}

// ---------------------------------

function explode_telefone($telefone)
{
    $partes = array(
        'ddd' => '',
        'telefone' => '',
    );

    $_telefone = explode(' ', $telefone);

    if (count($_telefone) == 2) {
        $partes['ddd'] = str_replace(array('-', '.', '(', ')'), null, trim($_telefone[0]));
        $partes['telefone'] = str_replace(array('-', ' ', '.'), null, trim($_telefone[1]));
    } else {
        $_telefone = str_replace(array('-', '.', '(', ')'), null, trim(array_shift($_telefone)));

        if (strlen($_telefone) >= 10) {
            $partes['ddd'] = substr($_telefone, 0, 2);
            $partes['telefone'] = substr($_telefone, 2);
        }
    }

    return $partes;
}

function asset($path)
{
    if (defined('BASE_URL_ASSETS') && strpos($path, BASE_URL_ASSETS) === false) {
        $path = BASE_URL_ASSETS . $path;
    }

    return $path;
}

/**
 * @param $carrinho Carrinho
 * @return array
 */
function getOpcoesPagamento($carrinho)
{
    $valor = $carrinho->getValorTotal();
    $numParcelasValor = getParcelasByValor($valor);
    $numParcelasProduto = $carrinho->getMaiorParcelaIndividual();
    $numParcelas = max($numParcelasValor, $numParcelasProduto);

    $response = array();
    for ($i = 1; $i <= $numParcelas; $i++) {
        $response[$i] = $valor / $i;
    }

    return $response;
}

/**
 * @param $carrinho Carrinho
 * @return array
 */
function getOpcoesPagamentoMultiplo($carrinho, $valorRestante)
{
    $valor = $valorRestante;
    $numParcelasValor = getParcelasByValor($valor);
    $numParcelasProduto = $carrinho->getMaiorParcelaIndividual();
    $numParcelas = max($numParcelasValor, $numParcelasProduto);

    $response = array();
    for ($i = 1; $i <= $numParcelas; $i++) {
        $response[$i] = $valor / $i;
    }

    return $response;
}

function getParcelasByValor($valor)
{

    $valor_minimo_parcelas = Config::get('valor_minimo_parcela') <= 0 ? 1 : Config::get('valor_minimo_parcela');

    // Pegando quantidade de parcelas (pode ser número quebrado)
    // Importante: Utiliza-se a função floor, pois é necessário sempre arrendondar o número quebrado para baixo
    // Assim tem-se certeza que será possível fazer o parcelamento daquele valor e não irá ultrapassar o valor mínimo da parcela.
    // Quando um $numParcelas quebrado passa do .5, ex.: 3.6 tem-se um número $numParcelas que fará com o que o valor das parcelas
    // seja maior que o ValorMinParcelas()
    $numParcelas = floor($valor / $valor_minimo_parcelas);

    // Se o $floatValor for maior que o getValorMinParcelas() então o resultado será menor que 1,
    // sendo necessário corrigir e informar que não haverá parcelamento
    if ($numParcelas < 1) {
        $numParcelas = 1;
    }

    if ($numParcelas > Config::get('numero_maximo_parcelas')) {
        $numParcelas = Config::get('numero_maximo_parcelas');
    }

    return $numParcelas;
}

function get_descricao_valor_parcelado($valor, $parcelas = null)
{
    if ($parcelas == null) {
        $parcelas = getParcelasByValor($valor);
    }

    $valorParcela = $valor / $parcelas;
    return $parcelas . 'x de R$ ' . format_money($valorParcela);
}

function icon($icon, $return = false)
{

    $icons = explode(' ', $icon);

    $iconName = "fa";
    foreach ($icons as $icon) {
        $iconName .= " fa-" . $icon;
    }

    if ($return) {
        return $iconName;
    }

    echo $iconName;
}
