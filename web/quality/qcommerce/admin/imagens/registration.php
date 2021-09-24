<?php

// Define o título da página
$pageTitle = 'Imagens';

// Obtém o contexto
$context = $_GET['context'];

// Define a classe de referencia para associação
$_class = $context . 'Arquivo';
$_classPeer = $_class . 'Peer';

$relation_local = $_GET['context'] . 'Id';

$context_field = $_classPeer::translateFieldName($relation_local, BasePeer::TYPE_PHPNAME, BasePeer::TYPE_FIELDNAME);

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
