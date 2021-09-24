<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_extrato' table.
 *
 * Tabela com os registros de entrada e saida de pontos
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ExtratoPeer extends BaseExtratoPeer
{
    const POSITIVO = '+';
    const NEGATIVO = '-';

    const PEDIDO = "PEDIDO";

    /**
     * @param $tipo string
     * @param $operacao + | -
     * @param $pontos double
     * @param $clienteId int
     * @param $observacao string
     * @param $pedidoId int
     * @param null $resgateId int
     * @param null $transferenciaId int
     * @return Extrato
     * @throws PropelException
     */
    public static function geraExtrato(
        $tipo,
        $operacao,
        $pontos,
        $clienteId,
        $observacao,
        $pedidoId = null,
        $resgateId = null,
        $transferenciaId = null
    ) {
        $extrato = new Extrato();
        $extrato->setTipo($tipo);
        $extrato->setOperacao($operacao);
        $extrato->setPontos($pontos);
        $extrato->setClienteId($clienteId);
        $extrato->setPedidoId($pedidoId);
        $extrato->setResgateId($resgateId);
        $extrato->setTransferenciaId($transferenciaId);
        $extrato->setData(new DateTime());
        $extrato->setObservacao($observacao);

        $extrato->save();

        return $extrato;
    }

    /**
     * @param Cliente $cliente
     * @param string $tipoExtrato
     * @param string|null $mes
     * @param string|null $ano
     */
    public static function desbloquearExtratoCliente(Cliente $cliente, string $tipoExtrato, string $mes = null, string $ano = null)
    {
        $mes = !$mes ? date('m') : $mes;
        $ano = !$ano ? date('Y') : $ano;

        $dataInicio = DateTime::createFromFormat('Y-m-d H:i:s', "{$ano}-{$mes}-01 00:00:00");
        $dataFim = (clone $dataInicio)->modify('last day of this month');
        $dataFim->setTime(23, 59, 59);

        $query = ExtratoQuery::create()
            ->filterByCliente($cliente)
            ->filterByBloqueado(true)
            ->filterByTipo($tipoExtrato)
            ->filterByData($dataInicio, Criteria::GREATER_EQUAL)
            ->filterByData($dataFim, Criteria::LESS_EQUAL)
            ->find();

        foreach ($query as $extrato) :
            $extrato->setBloqueado(false);
            $extrato->save();
        endforeach;
    }

}
