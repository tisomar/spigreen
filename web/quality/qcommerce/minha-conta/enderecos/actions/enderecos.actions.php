<?php
$arrEnderecos = EnderecoQuery::create()
        ->filterByClienteId(ClientePeer::getClienteLogado()->getId())
        ->orderById()
        ->find();
