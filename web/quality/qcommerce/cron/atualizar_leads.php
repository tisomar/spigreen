<?php

date_default_timezone_set("America/Sao_Paulo");
set_time_limit(0);
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
ini_set('memory_limit', '2048M');

require_once __DIR__ . '/../includes/include_config.inc.php';
//require_once __DIR__ . '/../includes/include_propel.inc.php';

$cronFile = __FILE__;

include_once __DIR__ . '/include/cron-init.inc.php';

include_once __DIR__ . '/../classes/IntegracaoMailforweb.php';
include_once __DIR__ . '/../classes/CorreiosEndereco.php';

$clientesPendente = ClienteDistribuidorQuery::create()
                        ->filterByTipoLead(null, Criteria::NOT_EQUAL)
                        ->filterByLead(1)
                        ->filterByClienteRedefacilId(null, Criteria::EQUAL)
                        ->find();
//var_dump($clientesPendente);die;
$horasDiferenca = (int)(_parametro('retorno_distribuidor')) - (int)(_parametro('alertar_distribuidor'));

$aviso = new DateTime('now');
//$aviso->sub(new DateInterval('P1D'));
$aviso->modify('-' . $horasDiferenca . ' Hours');

$umDiaDiff = new DateTime('now');
$umDiaDiff->sub(new DateInterval('P1D'));

foreach ($clientesPendente as $clientePendente) {
    /** @var ClienteDistribuidor $clientePendente */

    $cadastro = new DateTime($clientePendente->getDataAtualizacao('Y-m-d H:i:s'));

    echo "<pre>";
    var_dump($clientePendente->getCliente()->getNomeCompleto());
    var_dump($clientePendente->getDataAtualizacao('Y-m-d H:i:s'));
    var_dump($clientePendente->getNomeCompleto() . '-' . $clientePendente->getEmail());
    echo "</pre>";

    if ($cadastro->getTimestamp() < $umDiaDiff->getTimestamp()) {
        $oldDistribuidor = $clientePendente->getCliente();

        if ($clientePendente->getTipoLead() == 'D') {
            $novoDistribuidor = ClienteDistribuidorPeer::getDistribuidorMaisPertoCron($clientePendente->getCep());
        } else {
            $novoDistribuidor = ClienteDistribuidorPeer::getDistribuidorMaisPertoProdutoCron($clientePendente->getCep());
        }

        ClienteDistribuidorPeer::notificarAoDistribuidorDoClienteAposVencimentoDaDataCron($clientePendente, $oldDistribuidor);
        ClienteDistribuidorPeer::notificarAoClienteDoNovoDistribuidorAposVencimentoDaDataCron($clientePendente, $novoDistribuidor);
        if ($clientePendente->getTipo() == 'D') {
            ClienteDistribuidorPeer::notificarAoDistribuidorDoNovoClienteDistribuidorCron($novoDistribuidor);
        } else {
            ClienteDistribuidorPeer::notificarAoDistribuidorDoNovoClienteProdutoCron($novoDistribuidor);
        }

        $clientePendente->setCliente($novoDistribuidor);
        $clientePendente->setNotificacaoAlerta(false);
        $clientePendente->setDataCadastro(new DateTime());
        $clientePendente->save();

        echo "<pre>Modificação:<br>";
        echo "\n\n<br><br>";
        echo "</pre>";

        $novoDistribuidor->setDataUltimoLead(date('Y-m-d'));
        $novoDistribuidor->save();
    } elseif ($cadastro->getTimestamp() > $umDiaDiff->getTimestamp()
        && ($umDiaDiff->getTimestamp() - $cadastro->getTimestamp()) > ($umDiaDiff->getTimestamp() - $aviso->getTimestamp())
        && $clientePendente->getNotificacaoAlerta() == false) {
        echo "<pre>Notificação:<br>";
        echo "\n\n<br><br>";
        echo "</pre>";

        $clientePendente->setNotificacaoAlerta(true);
        $clientePendente->save();

        ClienteDistribuidorPeer::notificarAoDistribuidorDoClienteAntesVencimentoDaDataCron($clientePendente);
    }
}

include_once __DIR__ . '/include/cron-stop.inc.php';
