<?php
if (Config::get('mostrar_marcas') == false) {
    redirect_404();
}

$page   = isset($args[0]) && is_numeric($args[0]) && $args[0] > 0 ? $args[0] : 1;

# caso esteja em uma paginação, desabilita a indexacao desta página
if ($page > 1) {
    $meta['noindex'] = false;
}

$pager  = MarcaQuery::create()->orderByNome()->paginate($page, 24);
