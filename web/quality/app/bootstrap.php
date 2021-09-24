<?php

// Adicionar o controlador antigo para mantenimento das funcionalidades
// Removi muitas funcionalidades no qual terei que habilitá-las e aos poucos remove-las
include_once 'bootstrap.controlador.php';

// Ocorreu um erro referente a strtolower, no qual quebrava caracteres, e esta configuração soluciona este problema
mb_internal_encoding('UTF-8');

// A bomba de constantes
require_once QCOMMERCE_DIR . DS . 'includes' . DS . 'include_config.inc.php';

use Symfony\Component\Routing;
use Symfony\Component\HttpFoundation\Response;
use QPress\Router\RouteResolver;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Debug\Debug;
use Doctrine\Common\Annotations\AnnotationRegistry;

// Em caso de ser ambiente de desenvolvimento
if (ENV == 'dev') :
    Debug::enable();
endif;

// Tentar localizar a rota referente a QualityPress
try {
    AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
    $kernel = new AppKernel(ENV, ('prod' != ENV));
    // $kernel->loadClassCache();

    // $kernel = new AppCache($kernel);
    // When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
    // Request::enableHttpMethodParameterOverride();

    // Normalmente o segundo e o terceiro parâmetro não são enviados
    // Foram acrescentados neste caso específicos para geração do erro
    // OBS: Com o erro, o roteador da QualityPress é chamado
    $response = $kernel->handle($request, HttpKernelInterface::MASTER_REQUEST, false);

    // Imprimir resposta na tela
    $response->send();
    $kernel->terminate($request, $response);
}

// No caso de erro 404, tratado pelo Kernel do Symfony, trabalhar com o Router da QualityPress
catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
    \QPress\CSRF\NoCSRF::enableOriginCheck();
    $root_path = ROOT_PATH;

    $router = new RouteResolver($request);
    $args = $router->getArguments();
    $file = $router->getFile();
    
    $isAppDevAccess = basename($container->getRequest()->server->get('SCRIPT_FILENAME')) == 'app_dev.php';

    if (!$router->isAdmin() && Config::get('modo_manutencao') && (!$isAppDevAccess) /*&& ENV == 'prod'*/) :
        $file = $request->server->get('DOCUMENT_ROOT') . $request->getBaseUrl() . '/manutencao.php';
    endif;

    if ($router->isAdmin() && $router->getModule() != 'secure') :
        require_once QCOMMERCE_DIR . '/admin/includes/security.inc.php';
    endif;

    // Inicialização de containers e providers externos
    include_once __DIR__ . '/config/pimple.php';

    ob_start();
    include $file;
    $response = new Response(ob_get_clean(), Response::HTTP_OK, array('charset' => 'UTF-8'));
    $response->send();
}

// Erro de código
catch (\Exception $e) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    if (ENV == 'dev') {
        throw $e;
    }

    ob_start();
    include(QCOMMERCE_DIR . '/erro-interno/index.php');
    $response = new Response(ob_get_clean(), Response::HTTP_OK, array('charset' => 'UTF-8'));
    $response->send();
}
