<?php

require 'util/pdo.php';

$pdo = PDOConnection::getInstance();

$user_add = array(
    'status'    => 1,
    'typing'    => 0,
    'name'      => 'Quality Press',
    'level'     => 1,
    'email'     => 'contato@qualitypress.com.br',
    'photo'     => NULL,
    'user'      => 'qpress',
    'password'  => md5('qpress123456'),
    'time'      => 0
);  

$stmt = $pdo->prepare('SELECT COUNT(*) as RESULT FROM brtalk_user WHERE user = :user');
$stmt->bindValue('user', $user_add['user']);
$stmt->execute();

if ($stmt->fetchObject()->RESULT == 0) {
    $pdo->prepare('INSERT INTO brtalk_user (
		status, typing, level, name, email, photo, user, password, register_date, time
	) VALUES (
		:status, :typing, :level, :name, :email, :photo, :user, :password, NOW(), :time
	)')->execute($user_add);
}