<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 19/06/2018
 * Time: 11:56
 */

use QPress\Mailing\Mailing;

set_time_limit(0);
ini_set('memory_limit', '-1');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/** Constantes da cron
 *
 * Variáveis fixas que serão usadas na cron
 * $configPontuaçãoMensal é o objeto com todas as configurações.
 * $dataAtual é a data vigente no dia da cron
 * $primeiroDiaMes é o primeiro dia do mês que a cron está rodando.
 * $dataValidate serve para validar se entrou em alguma validação para enviar o aviso
 *
 */
$configPontuacaoMensal = ConfiguracaoPontuacaoMensalQuery::create()->findOneById(1);
$dateAtual = DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
$primeiroDiaMes = DateTime::createFromFormat('Y-m-d', date('Y-m') . '-01');
$dataValidate = '';
$aviso = '';

$dataAviso1 = DateTime::createFromFormat('Y-m-d', date('Y-m') . '-' . $configPontuacaoMensal->getDiaAviso1());

if ($dateAtual->format('Y-m-d') ==  $dataAviso1->format('Y-m-d')) {
    $dataValidate = $dataAviso1->format('Y-m-d');
    $aviso = 1;
}

if (empty($dataValidate)) {
    if ($configPontuacaoMensal->getTipoAviso2() == 1) {
        $dataAviso2 = DateTime::createFromFormat('Y-m-d', date('Y-m') . '-' . '01');
        $dataAviso2->add(new DateInterval('P1M'));
        $dataAviso2->sub(new DateInterval('P' . $configPontuacaoMensal->getDiaAviso2() . 'D'));
    } else {
        $dataAviso2 = DateTime::createFromFormat('Y-m-d', date('Y-m') . '-' . $configPontuacaoMensal->getDiaAviso2());
    }
    if ($dateAtual->format('Y-m-d') ==  $dataAviso2->format('Y-m-d')) {
        $dataValidate = $dataAviso2->format('Y-m-d');
        $aviso = 2;
    }
}

if (!empty($dataValidate)) {
    $clientesList = ClienteQuery::create()->find();
    foreach($clientesList as $clienteEmail) :
        
        $clienteAtivo = ClientePeer::getClienteAtivoMensal($clienteEmail->getId());
        if (!$clienteAtivo) :
            $objCliente = ClienteQuery::create()->findOneById($clienteEmail->getId());
            $valorPontos = PedidoPeer::getValorTotalPontosPedidosMesAtual($clienteEmail->getId());
        
            $objAvisoCompra = new CronlogAvisoCompraMensal();
            $objAvisoCompra->setClienteId($objCliente->getId());
            $objAvisoCompra->setData($dateAtual->format('Y-m-d'));
            $objAvisoCompra->setAviso($aviso);
            $objAvisoCompra->setValorCompra($valorPontos);

            if ($aviso == 1) :
                $objAvisoCompra->setMensagem($configPontuacaoMensal->getDescricaoAviso1());
                $objAvisoCompra->setTitulo($configPontuacaoMensal->getAssuntoAviso1());
                \QPress\Mailing\Mailing::enviaAviso1ClienteSemCompra($objCliente, $configPontuacaoMensal);
                echo json_encode('Emails de aviso 1 enviados');
            elseif ($aviso == 2) :
                $objAvisoCompra->setMensagem($configPontuacaoMensal->getDescricaoAviso2());
                $objAvisoCompra->setTitulo($configPontuacaoMensal->getAssuntoAviso2());
                \QPress\Mailing\Mailing::enviaAviso2ClienteSemCompra($objCliente, $configPontuacaoMensal);

                echo json_encode('Emails de aviso 2 enviados');
            endif;

            $objAvisoCompra->save();
        endif;
    endforeach;
}
