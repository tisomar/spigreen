<?php

$config['routes'] = array(
    'list' => get_url_admin() . '/' . $router->getModule() . '/list/?context=' . $_GET['context'] . '&reference=' . $_GET['reference'],
);

if (Config::get('produto.proporcao') == '1:1') {
    $imageDim = '1024x1024';
    $imageRatio = 1;
} elseif (Config::get('produto.proporcao') == '4:3') {
    $imageDim = '1024x768';
    $imageRatio = '1.33';
} elseif (Config::get('produto.proporcao') == '3:4') {
    $imageDim = '768x1024';
    $imageRatio = '0.75';
}
$config['dimensao'] = array(
    ProdutoPeer::OM_CLASS => $imageDim . ' pixels',
);
