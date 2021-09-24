<?php

$idDeleteBanco = !empty($_POST['idBanco']) ? $_POST['idBanco'] : null;
$con = Propel::getConnection();
$response = [];
if($idDeleteBanco != null) {
    BancoCadastroClienteQuery::create()
        ->filterById($idDeleteBanco)
        ->delete($con);

    $response = ['response' => '200'];
}

echo json_encode($response);