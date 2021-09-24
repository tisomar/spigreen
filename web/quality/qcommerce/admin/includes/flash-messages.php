<?php
use QPress\Template\Widget;

$titles_map = array(
    'warning' => "Atenção!",
    'error' => "Ops! Alguns erros foram encontrados.",
    'success' => "Sucesso!",
    'info' => "Informação!",
);

foreach ($session->getFlashBag()->all() as $type => $messages) {
     Widget::render('admin/flash_messages', array(
                 'title' => $titles_map[$type],
                 'type' => $type,
                 'content' => implode('<br />', $messages),
             ));
}
