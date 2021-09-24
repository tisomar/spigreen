<?php

use PFBC\Element;


if ($context == GaleriaPeer::OM_CLASS) {
    $a = '<a class="btn %s" href="%s">%s</a>&nbsp;';

    // Produtos ---
    $html = new Element\HTML(sprintf($a, 'btn-green', $config['routes'][$context]['registration'], '<span class="icon-folder-open"></span> <span class="hidden-xs">Gerenciar Galeria</span>'));
    $html->render();
}


$add = new \PFBC\Element\AddNewButton($config['routes']['registration']);
$add->render();
