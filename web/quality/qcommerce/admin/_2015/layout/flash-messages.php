<?php
use QPress\Template\Widget;

$titles_map = array(
    'warning' => "Atenção!",
    'error' => "Alguns erros foram encontrados.",
    'success' => "Sucesso!",
    'info' => "Informação!",
);

$icons_map = array(
    'warning' => "icon-exclamation-sign",
    'error' => "icon-remove-sign",
    'success' => "icon-ok-sign",
    'info' => "icon-info-sign",
);

foreach ($session->getFlashBag()->all() as $type => $messages) {
    Widget::render('admin/flash_messages', array(
        'icon' => $icons_map[$type],
        'title' => $titles_map[$type],
        'type' => $type,
        'content' => implode('<br />', $messages),
    ));
}
