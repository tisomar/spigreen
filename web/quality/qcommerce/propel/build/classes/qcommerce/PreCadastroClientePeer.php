<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use function Sodium\add;


/**
 * Skeleton subclass for performing query and update operations on the 'qp1_pre_cadastro_cliente' table.
 *
 * Quando o cliente for ativo em um pré cadastro
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class PreCadastroClientePeer extends BasePreCadastroClientePeer
{
    /**
     * @param Cliente $objCliente
     * @return array|bool
     * @throws PropelException
     */
    public static function cadastrarClientePreCadastro(Cliente $objCliente)
    {

        $preTipo = Config::get('precadastro.tipo');
        $preDias = Config::get('precadastro.dias_corridos');
        $preData = Config::get('precadastro.data_final');

        $dateNow = new DateTime(date('Y-m-d') . ' 00:00:00');

        if ($preTipo == 'dias') {
            $dateNow->add(new DateInterval('P' . $preDias . 'D'));
            $dateActive = $dateNow;
        } elseif ($preTipo == 'data') {
            $datePre = new DateTime($preData . ' 00:00:00');
            if ($dateNow->getTimestamp() <= $datePre->getTimestamp()) {
                $dateActive = $datePre;
            } else {
                return false;
            }
        }

        $objPreCadastroCliente = new PreCadastroCliente();
        $objPreCadastroCliente->setCliente($objCliente);
        $objPreCadastroCliente->setTipo($preTipo);
        $objPreCadastroCliente->setDataInicio(date('Y-m-d h:i:s'));
        $objPreCadastroCliente->setDataFinalizacao($dateActive->format('Y-m-d h:i:s'));
        $objPreCadastroCliente->save();
        $objPatrocinadorCliente = $objCliente->getPatrocinadorDireto();
        $con = Propel::getConnection();

        // TODO: update this in future to use constructor
        $logger = new Logger('debug-channel');
        $logger->pushHandler(new StreamHandler('debug_app.log', Logger::DEBUG));
        $gerenciadorRede = new GerenciadorRede($con, $logger);
        //$gerenciadorRede->insereRedePreCadastro($objCliente, $objPatrocinadorCliente);
        $gerenciadorRede->insereRede($objCliente, $objPatrocinadorCliente, false);
    }
}
