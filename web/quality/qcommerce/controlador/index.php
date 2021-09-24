<?php

use QPress\Container\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Debug\Debug;
use QPress\Router\RouteResolver;
// Configurações
/*
$configDir = __DIR__ . '/../../app/config/';

$locator    = new FileLocator($configDir);
//$locator  = $locator->locate(sprintf('config_%s.yml', $env), null, false);

$loaderResolver = new LoaderResolver(array(new YamlSecureLoader($locator)));
$delegatingLoader = new DelegatingLoader($loaderResolver);

$configData = $delegatingLoader->load($configDir . sprintf('config_%s.yml', $env));
if (true == $configData['secure']['use_ssl']) {
    if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == ""){
        $redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        header("HTTP/1.1 307 Temporary Redirect");
        header("Location: $redirect");
        exit;
    }
}*/

setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
date_default_timezone_set('America/Sao_Paulo');

if (get_magic_quotes_gpc()) {
    function stripslashes_gpc(&$value)
    {
        $value = stripslashes($value);
    }
    array_walk_recursive($_GET, 'stripslashes_gpc');
    array_walk_recursive($_POST, 'stripslashes_gpc');
    array_walk_recursive($_COOKIE, 'stripslashes_gpc');
    array_walk_recursive($_REQUEST, 'stripslashes_gpc');
}

global $strIncludesKey;
global $container;

// Container
$container = new Container();

// Request
$request = $container->getRequest();
$request->setDefaultLocale('pt_BR');

/**
 * Verifica o redirecionamento das páginas
 */
if (!defined('HAS_SSL_INSTALLED')) {
    define('HAS_SSL_INSTALLED', false);
}
if (HAS_SSL_INSTALLED) {
    // Verifica se o cliente está no carrinho, login, checkout e admin e redireciona para https.
    // Do contr?rio redireciona para http.
    $path = str_replace($request->getBaseUrl(), '', $request->getRequestUri());
    $sslPath = array(
        'carrinho',
        'login',
        'checkout',
        'minha-conta',
        'admin',
        'ajax'
    );

    // Verifica a primeira pasta que o site est? acessando.
    list($module,) = explode('/', ltrim($path, '/'));

    // Se n?o for localhost
    if (!in_array($request->getClientIp(), array('::1', '127.0.0.1')) && $request->getMethod() != 'POST') {
        // verifica se a p?gina necessita de SSL e redireciona
        if (in_array($module, $sslPath)) {
            if (!$request->isSecure()) {
                header('Location: https://' . $request->getHost() . $request->getRequestUri());
                exit;
            }
        } else {
            if ($request->isSecure()) {
                header('Location: http://' . $request->getHost() . $request->getRequestUri());
                exit;
            }
        }
    }
}

// Session
$session = $container->getSession();

// Environment
$env = pathinfo($request->server->get('SCRIPT_FILENAME'), PATHINFO_FILENAME) == 'app' ? 'prod' : 'dev';

Debug::enable();

// Error Handler
$errorHandler = new \QPress\ErrorHandler\ErrorHandler();
$errorHandler->register($env);

$isLightbox = $request->query->get('isLightbox') == "true";

try {
    \QPress\CSRF\NoCSRF::enableOriginCheck();

    // Other Libs
    require __DIR__ . '/../includes/include_config.inc.php';
    $root_path = ROOT_PATH;
//    $distribuidores_root_path = $root_path.'/distribuidores';
//    $distribuidores_root_path_scritps = $root_path.'/distribuidor_scripts';
//    $distribuidores_root_path_novo = $root_path.'/distribuidores_novo';


    $router = new RouteResolver($request);
    $args = $router->getArguments();
    $file = $router->getFile();

    if (!$router->isAdmin() && Config::get('modo_manutencao') && $env == 'prod') {
        $file = $request->server->get('DOCUMENT_ROOT') . $request->getBaseUrl() . '/manutencao.php';
    }

    if ($router->isAdmin() && $router->getModule() != 'secure') {
        require_once QCOMMERCE_DIR . '/admin/includes/security.inc.php';
    }

    ob_start();
    require $file;
    $response = new Response(ob_get_contents(), Response::HTTP_OK, array('charset' => 'UTF-8'));
    ob_end_clean();

    $response->send();
} catch (PropelException $e) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    if ($env == 'dev') {
        throw $e;
    } else {
        include(__DIR__ . '/../erro-interno/index.php');
        //echo 'erro interno no servidor';
    }
} catch (Exception $e) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    if ($env == 'dev') {
        throw $e;
    } else {
        include(__DIR__ . '/../erro-interno/index.php');
        //echo 'erro interno no servidor';
    }
}
