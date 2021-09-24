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
 *
 * $configPontuaçãoMensal é o objeto com todas as configurações.
 * $dataAtual é a data vigente no dia da cron para validar se está rodando no dia correto
 * $primeiroDiaMes é o primeiro dia do mês que a cron está rodando (validar com data atual.
 * $primeiroDiaMesAnterior primeiro dia do mês anterior para desativar
 * $ultimoDiaMesAnterior ultimo dia do mês anterior para desativar
 *
 */
$configPontuacaoMensal = ConfiguracaoPontuacaoMensalQuery::create()->findOneById(1);

$dateAtual = DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
$primeiroDiaMes = DateTime::createFromFormat('Y-m-d', date('Y-m') . '-01');

$primeiroDiaMesAnterior = DateTime::createFromFormat('Y-m-d', date('Y-m') . '-01');
$primeiroDiaMesAnterior->sub(new DateInterval('P1M'));

$ultimoDiaMesAnterior = DateTime::createFromFormat('Y-m-d', date('Y-m') . '-01');
$ultimoDiaMesAnterior->sub(new DateInterval('P1D'));



if ($dateAtual->format('Y-m-d') == $primeiroDiaMes->format('Y-m-d')) {
    $queryClientesZerar = '
                            select
                                cli.ID,
                                coalesce (ped.total, 0) as total_compra
                            from qp1_cliente cli
                            left join (
                                        select
                                          qp1_pedido.CLIENTE_ID,
                                          coalesce(sum(qp1_pedido.VALOR_ITENS + qp1_pedido.VALOR_ENTREGA - (case 
                                                            when qp1_pedido.CUPOM <> null then qp1_pedido.VALOR_CUPOM_DESCONTO
                                                            else 0
                                                            end)),0) as total
                                        from qp1_pedido
                                        where
                                          qp1_pedido.CREATED_AT between "' . $primeiroDiaMesAnterior->format('Y-m-d') . ' 00:00:00"
                                            and "' . $ultimoDiaMesAnterior->format('Y-m-d') . ' 23:59:59"
                                        group by 
                                            qp1_pedido.CLIENTE_ID
                                          ) as ped
                                on cli.ID = ped.CLIENTE_ID
                            where 
	                            cli.tree_left > 0
	                            and cli.VAGO = 0
                            having
                                total_compra <= "' . $configPontuacaoMensal->getValorCompra() . '"';

    $con = Propel::getConnection();

    $stmt = $con->query($queryClientesZerar);
    $stmt->execute();

    $arrClientes = array();

    while ($row = $stmt->fetch()) {
        $arrClientes[] = $row['ID'];
        $objBloqueioCompra = new BloqueioCompraMensal();
        $objBloqueioCompra->setClienteId($row['ID']);
        $objBloqueioCompra->setData($dateAtual->format('Y-m-d'));
        $objBloqueioCompra->setValorCompra($row['total_compra']);
        $objBloqueioCompra->save();
    }

    if (count($arrClientes) > 0) {
        ClienteQuery::create()->filterById($arrClientes, Criteria::IN)->update(array('NaoCompra' => 1));
        ClienteQuery::create()->filterById($arrClientes, Criteria::NOT_IN)->update(array('NaoCompra' => 0));
    }
}
