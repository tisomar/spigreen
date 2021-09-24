<?php

use PFBC\Element;
$a = '<a class="btn %s" href="%s">%s</a>&nbsp;';

if (!$object->isNew()) {
    // Faixas de Peso ---
    $html = new Element\HTML(sprintf($a, 'btn-default', get_url_admin() . '/faixa-peso/list/?context=' . $_class . '&reference=' . $object->getId(), '<span class="icon-list"></span> <span class="hidden-xs"> Faixas de Peso</span>'));
    $html->render();
}

$back = new Element\BackButton($config['routes']['list']);
$back->render();
