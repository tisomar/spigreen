<?php
require '../util/header.php';
require 'includes.php';

$session = new Session();
$session->checkSession('user');

$vld = new Validation();

$vld->Validate();

if($vld->hasErrors() == false){

	extract($_POST, EXTR_SKIP);
	
	$pdo = PDOConnection::getInstance();
	
	$user_update = array(
		'user_id' => $user_id,
		'status' => 0
	);
	
	$pdo->prepare('UPDATE brtalk_user SET status = :status WHERE user_id = :user_id')->execute($user_update);

	$vld->addMessage('Usu�rio exclu�do com sucesso');
		
}

$vld->jsonResult();
?>