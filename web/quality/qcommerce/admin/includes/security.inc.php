<?php
@session_cache_expire(480);

if (!UsuarioPeer::isAuthenticad()) {
    $_pathinfo = $container->getRequest()->server->get('PATH_INFO');
    $_queryString = $container->getRequest()->server->get('QUERY_STRING');
    $_queryString = $_queryString == "" ? '' : '?' . $_queryString;
    $_lastpage = $_pathinfo . $_queryString;

    $container->getSession()->set('admin.lastpage', $_lastpage);
    redirect('/admin/secure/login');
} else {
}
