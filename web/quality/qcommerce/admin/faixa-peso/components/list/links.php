<?php
use PFBC\Element;
if ($_GET['context'] == TransportadoraRegiaoPeer::OM_CLASS) {
    $a = '<a class="btn btn-default %s" href="%s">%s</a>&nbsp;';

    // Região ---
    $html = new Element\HTML(sprintf($a, 'btn-default', get_url_admin() . '/transportadora-regiao/registration/?id=' . $_GET['reference'], '<span class="icon-globe"></span><span class="hidden-xs"> Região</span>'));
    $html->render();

    $html = new Element\HTML(sprintf($a, 'btn-inverse open-in-modal', get_url_admin() . '/faixa-peso/importar/?context=' . $_GET['context'] . '&reference=' . $_GET['reference'] . '&iframe=true&width=80%&height=90%', '<span class="icon-upload"></span> <span class="hidden-xs"> Importar</span>'));
    $html->render();
}

$add = new \PFBC\Element\AddNewButton($config['routes']['registration']);
$add->render();
