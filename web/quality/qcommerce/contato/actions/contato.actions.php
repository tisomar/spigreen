<?php

// Verificar eventos da página
$pageEvents = \Config::get('facebook_tracking.todas_as_paginas.events');

// No caso de ser enviado com sucesso, deve ser lead tracking
if (false !== strpos($container->getRequest()->getRequestUri(), 'enviado-com-sucesso')) {
    $pageEvents = \Config::get('facebook_tracking.lead.events');
}

if ($container->getRequest()->getMethod() == 'POST') {
    FlashMsg::clear();

    $requiredFields = array(
        'nome' => 'O campo nome é obrigatório',
        'email' => 'O campo e-mail é obrigatório',
        'mensagem' => 'O campo mensagem é obrigatório',
    );

    $request = $container->getRequest()->request->all();
    $dataForm = array();
    foreach ($request['contato'] as $index => $value) {
        $dataForm[$index] = htmlspecialchars(stripslashes(trim(filter_var($value, FILTER_SANITIZE_STRING))));
    }

    foreach ($requiredFields as $requiredFieldName => $requiredMessageField) {
        if (!isset($dataForm[$requiredFieldName]) || trim($dataForm[$requiredFieldName]) == '') {
            FlashMsg::add('danger', $requiredMessageField);
        } elseif ($requiredFieldName == 'email' && valida_email($dataForm[$requiredFieldName]) == false) {
            FlashMsg::add('danger', 'Você deve informar um e-mail válido');
        }
    }

    if (FlashMsg::hasErros() == false) {
        if ($container->getRequest()->request->get('receber-newsletter') != false) {
            NewsletterPeer::save($dataForm['email'], $dataForm['nome']);
        }

        try {
            \QPress\Mailing\Mailing::enviarContato($dataForm);
            FlashMsg::success('Sua mensagem foi enviada com sucesso. Agradecemos o contato.');
            redirect('/contato/enviado-com-sucesso');
            exit;
        } catch (Exception $e) {
            FlashMsg::erro('Sua mensagem não pode ser enviada. Por favor, tente novamente mais tarde.');
        }
    }
}


// Busca os textos para os conteúdos localizados
$conteudo['contato_atendimento_online'] = ConteudoPeer::get('contato_atendimento_online');
$conteudo['contato_fale_conosco'] = ConteudoPeer::get('contato_fale_conosco');

// Busca dos eventos
$events = \QPress\Facebook\Tracking::getInstance()->getEvents($pageEvents);
