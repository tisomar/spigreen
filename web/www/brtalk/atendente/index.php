<?php

require 'includes.php';

$tpl = new Template();
$vld = new Validation();

$tpl->assign('system_name', $systemName);
$tpl->assign('system_version', $systemVersion);

$pdo = PDOConnection::getInstance();

if (isset($_POST['login']))
{

    $vld->Validate();

    if ($vld->hasErrors() == false)
    {

        extract($_POST, EXTR_SKIP);

        $md5 = md5($password);

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM brtalk_user WHERE user = :user AND password = :password AND status = :status');
        $stmt->bindParam('user', $user);
        $stmt->bindParam('password', $md5);
        $stmt->bindValue('status', 1);
        $stmt->execute();

        if ($stmt->fetchColumn() > 0)
        {

            $stmt = $pdo->prepare('SELECT COUNT(*) FROM brtalk_user WHERE user = :user  AND time > :time AND status = :status');
            $stmt->bindParam('user', $user);
            $stmt->bindValue('time', $lifeTime);
            $stmt->bindValue('status', 1);
            $stmt->execute();

            if ($stmt->fetchColumn() == 0)
            {

                $stmt = $pdo->prepare('SELECT * FROM brtalk_user WHERE user = :user AND password = :password AND status = :status LIMIT 1');
                $stmt->bindParam('user', $user);
                $stmt->bindParam('password', md5($password));
                $stmt->bindValue('status', 1);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                $session = new Session();
                $session->register('user', $user);

                header('Location: main.php');
                exit();
            }
            else
            {
                $vld->addError('Já existe um usuário logado com os dados informados');
            }
        }
        else
        {
            $vld->addError('Usuário ou senha inválido');
        }
    }
}

$tpl->assign('error', $vld->getErrorsAsHtml());

$tpl->show();