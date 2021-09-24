<?php
include("../includes/config.inc.php");
include("../includes/security.inc.php");
include("../includes/funcoes.inc.php");

// CLASSES DESTA SEÇÃO
include("../classes/qpress_produto.class.php");

// VERIFICA SE A CLASSE ESTÁ CRIADA
if ($_SESSION["PRODUTOS"]) {
    $obj = unserialize($_SESSION["PRODUTOS"]);
} else {
    $obj = new Produto();
}

$result = $obj->excluirDescricoes($_GET["desc1_cod"]);

$_SESSION["PRODUTOS"] = serialize($obj);
