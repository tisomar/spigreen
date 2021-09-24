<?php
include("../includes/config.inc.php");
include("../includes/security.inc.php");
include("../includes/funcoes.inc.php");

// CLASSES DESTA SEÇÃO
include("../classes/qpress_categoria.class.php");

// VERIFICA SE A CLASSE ESTÁ CRIADA
if ($_SESSION["CATEGORIAS"]) {
    $obj = unserialize($_SESSION["CATEGORIAS"]);
} else {
    $obj = new Categoria();
}

$result = $obj->subcategoriasCategoria($_GET["cate1_cod"]);

while ($line = mysql_fetch_object($result)) {
    array_walk($line, 'toUtf8');
    $arr[] = $line;
}

$retorno = "{itens:" . json_encode($arr) . '}';
echo $retorno;

//$_SESSION["PRODUTOS"] = serialize($obj);
