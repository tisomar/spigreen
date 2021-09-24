<?php
require '../util/header.php';
require 'includes.php';

$session = new Session();
$session->checkSession('user');

$vld = new Validation();

$vld->Validate();

if($vld->hasErrors() == false){

	extract($_POST, EXTR_PREFIX_ALL, 'post');
	
	$pdo = PDOConnection::getInstance();
	
	$user_update = array(
		'user_id' => $post_user_id,
		'name' => $post_name,
		'level' => $post_level,
		'email' => $post_email
	);
	
	$pdo->prepare('UPDATE brtalk_user SET name = :name, email = :email, level = :level WHERE user_id = :user_id')->execute($user_update);

	$vld->addMessage('Usuário atualizado com sucesso');
		
}

$vld->jsonResult();
?>