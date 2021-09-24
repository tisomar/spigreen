<?php

require_once __DIR__ . '/../../includes/security.php';

//var_dump(BASE_URL_ASSETS);die;
//var_dump(__DIR__ . '/../../includes/security.php');
//var_dump(__DIR__ . '/../../distribuidores/views/includes/filter.php');die;
require_once __DIR__ . '/actions/index.action.php';

$page = 'atividades';


$template = __DIR__ . '/views/index.inc.php';

$novoUrl = $root_path . '/distribuidores_novo/eventos/cadastro';

require  __DIR__ . '/../includes/layout.inc.php';
