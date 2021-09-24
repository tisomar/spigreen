<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 16/05/2018
 * Time: 15:22
 */

if (ClientePeer::isAuthenticad()) {
    echo json_encode(array(
       'retorno'   => ClientePeer::getClienteLogado(true)->getId()
    ));
} else {
    echo json_encode(array(
        'retorno'   => -1
    ));
}
