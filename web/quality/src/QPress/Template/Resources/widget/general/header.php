<?php
use QPress\Template\Widget;

if (Config::get('sistema.versao_demo')) {
    Widget::render('general/topo-demo');
}

// horizontal, default, vertical, center
Widget::render('general/header-vertical');