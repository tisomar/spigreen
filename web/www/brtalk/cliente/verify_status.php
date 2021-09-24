<?php
require '../util/header.php';
require 'includes.php';

$session = new Session();
$session->checkSession('client');

$client = $session->get('client');

$pdo = PDOConnection::getInstance();

/* Atualiza tempo cliente */
$stmt = $pdo->prepare('UPDATE brtalk_client SET time = :time WHERE client_id = :client_id LIMIT 1');
$stmt->bindValue('client_id', $client['client_id']);
$stmt->bindValue('time', $lifeTimeClient);
$stmt->execute();


/* Total da fila de espera */
$stmt = $pdo->prepare('SELECT COUNT(*) FROM brtalk_client WHERE client_id < :client_id AND status = :status AND time > :time');
$stmt->bindValue('client_id', $client['client_id']);
$stmt->bindValue('time', $lifeTime);
$stmt->bindValue('status', 1);
$stmt->execute();
$total = $stmt->fetchColumn();


/* Busca status do cliente */
$stmt = $pdo->prepare('SELECT * FROM brtalk_client WHERE client_id = :client_id');
$stmt->bindValue('client_id', $client['client_id']);
$stmt->execute();
$client_status = $stmt->fetch();


/* Inicia atendimento */
$user = NULL;

if($client_status['status'] == 2){

	$stmt = $pdo->prepare('SELECT * FROM brtalk_user WHERE user_id = :user_id');
	$stmt->bindValue('user_id', $client_status['user_id']);
	$stmt->execute();
	$user = $stmt->fetch();
	
	$session->register('client_user', $user);
}

$json = array(
	'total' => $total,
	'status' => $client_status['status'],
	'user' => $user
);

print json_encode($json);
?>