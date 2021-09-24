<?php
$countPedidos = PedidoQuery::create()->count();
$countClientes = ClienteQuery::create()->count();
