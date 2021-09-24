<?php
//include('../../../../includes/config.inc.php');
include_once('../../includes/include_propel.inc.php');
header("Content-type: text/xml");
    $strXml = '<?xml version="1.0" encoding="UTF-8"?>
<chart>
  <!--<message><![CDATA[You can broadcast any message to chart from data XML file]]></message> -->
	<series>';
            /* @var $pedido Pedido */
            /*$diaAtual = strtotime(date("Y-m-d"));
            $diaMesTras = strtotime("-30 days",date("Y-m-d"));*/
            $date = date("Y-m-d") ;

            //$oneWeekAgo = strtotime ( '-1 week' , strtotime ( $date ) ) ;
            $oneMonthAgo = strtotime('-1 month', strtotime($date)) ;
            
            $diferencaDias = round(abs(strtotime($date) - $oneMonthAgo) / 60 / 60 / 24);
            
            //echo $diferencaDias;
            
            $todosPedidos = PedidoQuery::create()->add(PedidoPeer::PEDI1_DAT, $oneMonthAgo, Criteria::GREATER_THAN)->addAnd(PedidoPeer::PEDI1_DAT, strtotime($date), Criteria::LESS_THAN)->orderByData()->find();
            //$diasNoMes = date("t");
            //echo Propel::getConnection()->getLastExecutedQuery();
            $j = 1;
for ($i = $diferencaDias; $i >= 0; $i--) {
    $novaData = strtotime("-$i days", strtotime(date("Y-m-d")));
    //echo date("Y-m-d",$novaData)."<br />";
    $strXml .= '<value xid="' . $j++ . '">' . date("d/m", $novaData) . '</value>';
}
            $pedidosArr = array();
            /* @var $objPedido Pedido */
if ($todosPedidos) {
    $j = 0;
    for ($i = $diferencaDias; $i >= 0; $i--) {
        if (isset($todosPedidos[$j])) {
            $pedidosArr[$todosPedidos[$j]->getData("d/m")]['quantidade'] += 1;
            $pedidosArr[$todosPedidos[$j]->getData("d/m")]['volume'] += $todosPedidos[$j]->getValorTotal();
            $j++;
        }
    }
}
    $strXml .= '</series>
	<graphs>
		<graph gid="1">';
if ($pedidosArr) {
    $j = 1;
    for ($i = $diferencaDias; $i >= 0; $i--) {
        $novaData = strtotime("-$i days", strtotime(date("Y-m-d")));
        if (isset($pedidosArr[date("d/m", $novaData)])) {
            $qtd = $pedidosArr[date("d/m", $novaData)]['quantidade'];
        } else {
            $qtd = '0';
        }
        $strXml .= '<value xid="' . $j++ . '">' . $qtd . '</value>';
    }
}
    $strXml .= '</graph>
		<graph gid="2">';
if ($pedidosArr) {
    $j = 1;
    for ($i = $diferencaDias; $i >= 0; $i--) {
        $novaData = strtotime("-$i days", strtotime(date("Y-m-d")));
        if (isset($pedidosArr[date("d/m", $novaData)])) {
            $vol = $pedidosArr[date("d/m", $novaData)]['volume'];
        } else {
            $vol = '0';
        }
        $strXml .= '<value xid="' . $j++ . '">' . $vol . '</value>';
    }
}
        $strXml .= '</graph>
	</graphs>
</chart>';
        //echo "<pre>";
        echo $strXml;
        //echo "</pre>";
