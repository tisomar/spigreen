<?php

    require_once __DIR__ . '/../../includes/security.php';
    require __DIR__ . '/actions/index.action.php';

require __DIR__ . '/actions/cadastro.geral.action.php';

    
    $title = 'Perfil';

    $template = __DIR__ . '/views/index.inc.php';
//var_dump($template);die;

    require  __DIR__ . '/../includes/layout.inc.php';
