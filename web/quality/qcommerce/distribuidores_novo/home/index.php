<?php
//    include(__DIR__. '/../../central/actions/video-vip-obtener-video-por-cliente.actions.php');
//    if ($videoVipMostrar && $video){
//        header('Location:' . ROOT_PATH . '/central/pontos/');
//        exit;
//    }

    require_once __DIR__ . '/../../includes/security.php';

require __DIR__ . '/actions/index.action.php';

    $title = 'Início';
    $template = __DIR__ . '/views/index.inc.php';

    require  __DIR__ . '/../includes/layout.inc.php';
