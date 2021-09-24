<?php
if (!defined('BASE_URL_ASSETS')) :
    if (Config::get('cloudflare.habilitado') == 1) :
        define('BASE_URL_ASSETS', '//' . Config::get('cloudflare.subdomain') . '.' . $request->getHttpHost() . BASE_PATH);
    else :
        define('BASE_URL_ASSETS', BASE_URL);
    endif;
endif;
