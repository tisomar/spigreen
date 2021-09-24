<?php
require 'util/pdo.php';
require 'util/settings.php';

$pdo = PDOConnection::getInstance();

$stmt = $pdo->prepare('SELECT COUNT(*) FROM brtalk_user WHERE time > :time AND status = :status');
$stmt->bindValue('time', $lifeTime);
$stmt->bindValue('status', 1);
$stmt->execute();

if($stmt->fetchColumn() == 0){
	print 'Offline';
}else{
	print 'Online';
}