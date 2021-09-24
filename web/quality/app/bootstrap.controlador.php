<?php

use QPress\Container\Container;

/**
 * Arquivo controlador antigo
 * Deve-se mantê-lo pois aos poucos o que há neste arquivo será eliminado, mantendo somente o bootstrap
 */
// Resolver problemas com o servidor
if (@get_magic_quotes_gpc()) {
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

$isSSLEnabled = (getenv('APPLICATION_SSL') == '0' || getenv('APPLICATION_SSL') == 'false') ? false : true;

### Definição de utilização do SSL
if ($isSSLEnabled) {
    // Verifica se o cliente está no carrinho, login, checkout e admin e redireciona para https.
    // Do contrario redireciona para http.
    
    $path = str_replace($request->getBaseUrl(), '', $request->getRequestUri());
    
    $sslPath = array(
        'carrinho',
        'login',
        'checkout',
        'minha-conta',
        'integracao',
        'admin',
        'ajax',
        'documentos' /* é necessario, pois existe um link para os termos de uso no cadastro e a pagina de cadastro é https */
    );

    // Verifica a primeira pasta que o site est? acessando.
    list($module,) = explode('/', ltrim($path, '/'));

    // Se não for localhost
    if (!in_array($request->getClientIp(), array('::1', '127.0.0.1')) && $request->getMethod() != 'POST') {
        // verifica se a p?gina necessita de SSL e redireciona
      /*  if (in_array($module, $sslPath)) {*/
        if (!$request->isSecure() || $request->server->get('HTTP_X_FORWARDED_PROTO') != 'https') {
            header('Location: https://' . $request->getHost() . $request->getRequestUri());
            exit;
        }
    /*    } else {
            if ($request->isSecure()) {
                header('Location: http://' . $request->getHost() . $request->getRequestUri());
                exit;
            }
        }*/
    }
}

// Session
$session = $container->getSession();

// Error Handler
$errorHandler = new \QPress\ErrorHandler\ErrorHandler();
$errorHandler->register(ENV);

$isLightbox = $request->query->get('isLightbox') == "true";
