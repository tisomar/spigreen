<?php
if ($request->getMethod() == 'POST' && $request->request->has('newsletter')) {
    $newsletter = array_map('trim', $request->request->get('newsletter'));
    $email = isset($newsletter['email']) ? $newsletter['email'] : null;
    $nome = isset($newsletter['nome']) ? $newsletter['nome'] : null;
    NewsletterPeer::save($email, $nome);
}
