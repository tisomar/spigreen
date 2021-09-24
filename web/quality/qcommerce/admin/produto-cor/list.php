<?php
$pageTitle = 'Biblioteca<span class="hidden-xs"> de </span> cores';
$_class = ProdutoCorPeer::OM_CLASS;
$preQuery = ProdutoCorQuery::create()->orderByNome();
$rowsPerPage = 12;
include QCOMMERCE_DIR . '/admin/_2015/load.page.php';
