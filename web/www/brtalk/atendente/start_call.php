<?php
require '../util/header.php';
require 'includes.php';

$session = new Session();
$session->checkSession('user');

$user = $session->get('user');

$client_id = isset($_POST['client_id']) ? (int) $_POST['client_id'] : 0;

$pdo = PDOConnection::getInstance();

$stmt = $pdo->prepare('SELECT * FROM brtalk_client WHERE client_id = :client_id LIMIT 1');
$stmt->bindValue('client_id', $client_id);
$stmt->execute();
$client = $stmt->fetch();

$call_status = 1;

switch((int) $client['status']){
	
	case 1: 
		
		$client_update = array(
			'client_id' => $client_id,
			'user_id' => $user['user_id'],
			'status' => 2
		);
		
		$pdo->prepare('UPDATE brtalk_client SET status = :status, user_id = :user_id, start_call_date = NOW() WHERE client_id = :client_id LIMIT 1')->execute($client_update);
		
		/* Hist�rico */
		$pdo->prepare('UPDATE brtalk_client_history SET status = :status, user_id = :user_id, start_call_date = NOW() WHERE client_id = :client_id LIMIT 1')->execute($client_update);
		
	break;
	case 2:
	
		$call_status = $client['user_id'] != $user['user_id'] ? 4 : 2;
		
	break;
	
}

$json = array('call_status' => $call_status);

print json_encode($json);
?>