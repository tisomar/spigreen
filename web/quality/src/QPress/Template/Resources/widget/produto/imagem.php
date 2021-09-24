<?php
use QPress\Template\Widget;

if (!isset($cor)) {
    $cor = null;
}
$collFotos = $objProduto->getFotosByCor($cor);

Widget::render('produto-detalhe/gallery', array(
    'collFotos'     =>  $collFotos,
));