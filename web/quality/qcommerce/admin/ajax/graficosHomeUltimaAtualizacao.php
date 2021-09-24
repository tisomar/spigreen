<?php

include("../includes/config.inc.php");
ini_set('max_execution_time', '60');

$graficos = GraficoQuery::create()->find();

$arrRetorno = array();

foreach ($graficos as $objGrafico) { /* @var $objGrafico Grafico */
    $arrRetorno[$objGrafico->getTipo()] = $objGrafico->getDataAtualizacao('d/m/Y H:i');
}

echo json_encode($arrRetorno);
