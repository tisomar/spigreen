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

$result = $obj->registroFoto($_GET["foto1_cod"]);

while ($line = mysql_fetch_object($result)) {
    array_walk($line, 'toUtf8');
    $arr[] = $line;
}

$retorno = "{itens:" . json_encode($arr) . '}';
echo $retorno;

$_SESSION["PRODUTOS"] = serialize($obj);
