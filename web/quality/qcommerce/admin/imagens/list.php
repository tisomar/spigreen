<?php

// Define o título da página
$pageTitle = 'Imagens';

// Obtém o contexto
$context = $_GET['context'];

// Define a classe de referencia para associação
$_class = $context . 'Arquivo';

// Define a classe repositório
$_classQuery = $_class . 'Query';

// Método de filtragem
$context_method = 'filterBy' . $context . 'Id';

// Pré condição de acordo com o contexto
$preQuery = $_classQuery::create()->$context_method($_GET['reference']);

include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
