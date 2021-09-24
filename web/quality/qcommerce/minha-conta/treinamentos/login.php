<?php

use GuzzleHttp\Exception\GuzzleException;

use ClickmarkApi\Credentials;
use ClickmarkApi\Models\User;
use ClickmarkApi\Rest\UserVerify;
use ClickmarkApi\Rest\UserRegistry;

$cliente = ClientePeer::getClienteLogado();

$user = new User($cliente->getNomeCompleto(), $cliente->getEmail(), $cliente->getSenha());

try {
    $mail = $user->getEmail();
    $verify = (new UserVerify($mail))->send();
    $verify = json_decode($verify);

    /**
     * verifica se existe o token para redirecionar para o login
     * se nao ele cadastra no sistema e depois redireciona
     */
    if (isset($verify->api_token)):
        header('Location: ' . Credentials::URL . 'auth/users/by-token/' . $verify->api_token);
    else:
        /**
         * registra o usuario
         */
        $response = (new UserRegistry($user))->send();
        $user = json_decode($response);
        
        var_dump($user);
        if (isset($user->api_token)):
            header('Location: ' . Credentials::URL . 'auth/users/by-token/' . $user->api_token);
        else:
            throw new Exception('Houve um erro no cadastro');
        endif;
    endif;

} catch (Exception $e) {
    echo $e->getMessage();
} catch (GuzzleException $e) {
    echo $e->getMessage();
}