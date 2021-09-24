<?php
require '../util/header.php';
require 'includes.php';

$session = new Session();
$session->checkSession('client');

$client = $session->get('client');

$pdo = PDOConnection::getInstance();

$typing = isset($_POST['typing']) ? (int) $_POST['typing'] : 0;

/* Envia mensagens */
if(isset($_POST['message'])){

	$user_id = (int) $_POST['user_id'];

	foreach($_POST['message'] as $message){
	
		$message_insert = array(
			'client_id' => $client['client_id'],
			'user_id' => $user_id,
			'type' => 1,
			'status' => 1,
			'message' => strip_tags($message)
		);
		
		$pdo->prepare('INSERT INTO brtalk_message (
			client_id, user_id, type, status, message, post_date
		) VALUES (
			:client_id, :user_id, :type, :status, :message, NOW()
		)')->execute($message_insert);
		
		/* Hist�rico */
		$pdo->prepare('INSERT INTO brtalk_message_history (
			client_id, user_id, type, status, message, post_date
		) VALUES (
			:client_id, :user_id, :type, :status, :message, NOW()
		)')->execute($message_insert);
		
	}
}

/* Atualiza cliente */
$stmt = $pdo->prepare('UPDATE brtalk_client SET time = :time, typing = :typing WHERE client_id = :client_id LIMIT 1');
$stmt->bindValue('client_id', $client['client_id']);
$stmt->bindValue('time', $lifeTimeClient);
$stmt->bindValue('typing', $typing);
$stmt->execute();

/* Busca status do cliente e dados do atendente */
$stmt = $pdo->prepare('SELECT c.status, u.typing FROM brtalk_client c LEFT JOIN brtalk_user u ON u.user_id = c.user_id WHERE c.client_id = :client_id');
$stmt->bindValue('client_id', $client['client_id']);
$stmt->execute();
$client_status = $stmt->fetch();

/* Lista mensagens */
$messages = array();
$stmt = $pdo->prepare('SELECT client_id, user_id, message FROM brtalk_message WHERE client_id = :client_id AND type = :type ORDER BY message_id ASC');
$stmt->bindValue('client_id', $client['client_id']);
$stmt->bindValue('type', 0);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Apaga mensagens lidas */ 
$stmt = $pdo->prepare('DELETE FROM brtalk_message WHERE client_id = :client_id AND type = :type');
$stmt->bindValue('client_id', $client['client_id']);
$stmt->bindValue('type', 0);
$stmt->execute();

$json = array(
	'client' => array('status' => $client_status['status']),
	'user' => array('typing' => $client_status['typing']),
	'messages' => $messages
);

print json_encode($json);
?>