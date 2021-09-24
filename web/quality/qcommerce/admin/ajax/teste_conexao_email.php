<?php

header('Content-Type: text/html; charset=UTF-8');

include '../includes/config.inc.php';

$postConfig = $_POST['configuracao'];

$strEmail = $postConfig['CONF1_EMA_ENV'];
$strSenha = $postConfig['CONF1_EMA_SEN'];

// instancio a classe para usar os dados de usuario e senha nao padrao
$objQmail = new Qmail('', '', $strEmail, $strSenha);

// setar a propriedade para da mensagem
$objQmail->setPara($strEmail);

if ($objQmail->envia('Email Teste', 'Teste realizado com sucesso!')) {
    echo '<span style="color: green" >Email enviado com sucesso!</span>';
} else {
    echo '<span style="color: red" >Não foi possível enviar o email.</span>';
}
