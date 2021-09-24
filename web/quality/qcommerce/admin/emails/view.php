<?php

$hash = $router->getArgument(0);

$objEmail = EmailLogQuery::create()
    ->where('md5(EmailLog.Id) LIKE ?', $hash)
    ->findOne();

if ($objEmail instanceof EmailLog) {
    echo $objEmail->getConteudo();
    exit;
} else {
    \QPress\Template\Widget::render('admin/template-empty', array(
        'title' => 'Email não encontrado',
        'content' => '<p>O sistema não encontrou o email solicitado e não conseguiu concluir a sua requisição.</p>'
    ));
}
