    <?php

    include("../includes/config.inc.php");
    ini_set('max_execution_time', '60');

    function blocoGrafico($data, $objAnalytics, $anId)
    {
        $strBloco = "";

        /* data do dia */
        $strDia = date("d/m", $data);
        $strData = date("Y-m-d", $data);
        $strDia .= ";";

        /* visita diaria do analytics */
        $dados = $objAnalytics->data($anId, 'ga:day', 'ga:visits', 'ga:day', $strData, $strData, 1, 1, '');

        $visitaDiaAnalytics = $dados[date("d", $data)]["ga:visits"];
        $visitaDiaAnalytics = ($visitaDiaAnalytics > 0) ? $visitaDiaAnalytics : "0";

        $strBloco .= $strDia . $visitaDiaAnalytics;

        return $strBloco;
    }

    $ultimoGrafico = GraficoQuery::create()->findOneByTipo('ANALYTICS');

    $today2 = strtotime($ultimoGrafico ? $ultimoGrafico->getDataAtualizacao("Y-m-d") : '');
    $today = strtotime(date("Y-m-d"));

    $diff = round(abs($today - $today2) / 60 / 60 / 24);

    $atualizarGrafico = $diff;

    if (($atualizarGrafico) || (!count($ultimoGrafico))) {
        $objAnalytics = new analytics_api();

        $objAnalytics->login($analyticsLogin, $analyticsPassword);
        $grafStr = "";

        $countDiasMes = date("t");

        $countDiasMes = round(abs($countDiasMes / 2));

        $data = strtotime(date("Y-m-d"));

        for ($i = $countDiasMes; $i >= 0; $i--) {
            $dataTemp = strtotime("-$i days", $data);
            $grafStr .= blocoGrafico($dataTemp, $objAnalytics, $analyticsId) . "\n";
        }

        $ultimoGrafico = (!($ultimoGrafico instanceof Grafico)) ? new Grafico() : $ultimoGrafico;
        $ultimoGrafico->setGraficoString($grafStr);
        $ultimoGrafico->setTipo('ANALYTICS');
        $ultimoGrafico->save();

        echo $ultimoGrafico->getGraficoString();
    } else {
        echo $ultimoGrafico->getGraficoString();
    }
