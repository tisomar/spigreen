<?php

if (!empty($_GET['filter'])) {
    $arrFilter = array_map('trim', $_GET['filter']);
} else {
    $arrFilter = array();
}

/*
    $query = ClienteQuery::create()
        ->limit(15);

    if (!empty($arrFilter['NomeRazaoSocial'])) {
        $query->filterByNomeRazaoSocial('%'.$arrFilter['NomeRazaoSocial'].'%');
    }

    if (!empty($arrFilter['DataIni'])) {
        $dataIni = DateTime::createFromFormat('d/m/Y', $arrFilter['DataIni']);
        $query->filterByDataCadastro($dataIni->format('Y-m-d 00:00:00'), Criteria::GREATER_EQUAL);
    }

    if (!empty($arrFilter['DataFin'])) {
        $dataFin = DateTime::createFromFormat('d/m/Y', $arrFilter['DataFin']);
        $query->filterByDataCadastro($dataFin->format('Y-m-d 23:59:59'), Criteria::LESS_EQUAL);
    }

    if (!empty($arrFilter['VIP'])) {
        $query->filterByVip($arrFilter['VIP']);
    }
*/
//--------------------------------------
//Estados

$arrEstados = EstadoQuery::create()->addAscendingOrderByColumn(EstadoPeer::SIGLA)->find()->getData();

foreach ($arrEstados as $itemEstado) {
    $estados[$itemEstado->getId()] = $itemEstado->getSigla();
}

//--------------------------------------
//Cidades
$arrCidades = array();

if (isset($arrFilter['cidade']) && $arrFilter['cidade'] > 0) {
    $arrCidades = CidadeQuery::create()->filterByEstadoId($arrFilter['estado'])->orderByNome()->find();
}

$cidades = array();
foreach ($arrCidades as $itemCidade) {
    $cidades[$itemCidade->getId()] = $itemCidade->getNome();
}

//--------------------------------------
//Produtos

$arrProdutos = ProdutoQuery::create()
    ->orderByNome(Criteria::ASC)
    ->find();
foreach ($arrProdutos as $itemProduto) {
    $produtos[$itemProduto->getId()] = $itemProduto->getNome();
}





//--------------------------------------
//Filtros

    $conSlave = Propel::getConnection();

    $objClienteLogado = ClientePeer::getClienteLogado();
    $objCliente       = ClientePeer::retrieveByPK($objClienteLogado->getId());

if (ClientePeer::getIsMaster()) {
    $clienteMaster = ClienteQuery::create()->findOne();
//        $levelCliente = $clienteMaster->getNrLevel();
    $leftValueCliente = $clienteMaster->getLeftValue();
    $rightValueCliente = $clienteMaster->getRightValue();
} else {
//        $levelCliente = $objCliente->getNrLevel();
    $leftValueCliente = $objCliente->getLeftValue();
    $rightValueCliente = $objCliente->getRightValue();
}


    $sqlComp = '';
    $sqlProduto = '';
    $sqlHaving = '';

if (isset($arrFilter['data_inicial']) && $arrFilter['data_inicial'] != '') {
    $data_inicial = DateTime::createFromFormat('d/m/Y', $arrFilter['data_inicial'])->format('Y-m-d 00:00:00');
    $sqlComp .= ' and DATE(qp1_cliente.created_at) >= "' . $data_inicial . '"';
}
if (isset($arrFilter['data_final']) && $arrFilter['data_final'] != '') {
    $data_final = DateTime::createFromFormat('d/m/Y', $arrFilter['data_final'])->format('Y-m-d 23:59:59');
    $sqlComp .= ' and DATE(qp1_cliente.created_at) <= "' . $data_final . '"';
}

//    if ($arrFilter['vip'] == 1){
//        $sqlComp .= ' and qp1_cliente.VIP = 1';
//    }else if ($arrFilter['vip'] == 0) {
//        $sqlComp .= ' and qp1_cliente.VIP = 0';
//    }


if (isset($arrFilter['estado']) && $arrFilter['estado'] > 0) {
    $sqlComp .= ' and qp1_cidade.ESTADO_ID = ' . $arrFilter['estado'];
}
if (isset($arrFilter['cidade']) && $arrFilter['cidade'] > 0) {
    $sqlComp .= ' and qp1_endereco.CIDADE_ID = ' . $arrFilter['cidade'];
}
if (isset($arrFilter['comprou']) && ($arrFilter['comprou'] == 's' || $arrFilter['comprou'] == 'n')) {
    if ($arrFilter['comprou'] == 's') {
        if (!empty($sqlHaving)) {
            $sqlHaving .= ' and ';
        }

        $sqlHaving .= ' totalCompras > 0';
    } else {
        if (!empty($sqlHaving)) {
            $sqlHaving .= ' and ';
        }

        $sqlHaving .= ' totalCompras is null';
    }
}
if (isset($arrFilter['produto']) && $arrFilter['produto'] > 0) {
    $sqlProduto .= ' and qp1_pedido_item.PRODUTO_ID = ' . $arrFilter['produto'];
}

    $nivelCliente = "";

if (isset($arrFilter['nivel_atingido']) && $arrFilter['nivel_atingido']) {
    if (!empty($sqlHaving)) {
        $sqlHaving .= ' and ';
    }

    $sqlHaving .= 'nivelCliente = "' . $arrFilter['nivel_atingido'] . '"';
}


//    if (isset($arrFilter['nivel']) && $arrFilter["nivel"] > 0) {
//
//        if(!empty($sqlHaving))
//            $sqlHaving .= ' and ';
//
//        $sqlHaving .= ' LEVEL = ' . $arrFilter["nivel"];
//    }

if (!empty($sqlHaving)) {
    $sqlHaving = ' having ' . $sqlHaving;
}



    $SQL = "select
                qp1_cliente.ID,
                DATE_FORMAT(qp1_cliente.created_at, '%d/%m/%Y') as created_at_f,
                qp1_cliente.NOME,
                qp1_cliente.EMAIL,
                qp1_cliente.TELEFONE,
                (
                    SELECT
                        SUM(qp1_pedido_item.VALOR_UNITARIO * qp1_pedido_item.QUANTIDADE)
                    FROM
                        qp1_pedido,
                        qp1_pedido_item
                    WHERE
                        qp1_pedido.CLIENTE_ID = qp1_cliente.ID
                        and qp1_pedido.ID = qp1_pedido_item.PEDIDO_ID
                        AND qp1_pedido.STATUS <> 'CANCELADO'
                ) AS totalCompras
            from
                qp1_cliente
                LEFT JOIN qp1_pedido ON (
                  qp1_pedido.CLIENTE_ID = qp1_cliente.ID
                )
                LEFT JOIN qp1_pedido_item ON (
                  qp1_pedido.ID = qp1_pedido_item.PEDIDO_ID
                ),
                qp1_endereco,
                qp1_cidade
            where
                qp1_endereco.CLIENTE_ID = qp1_cliente.ID
                and qp1_cliente.ID <> " . $objClienteLogado->getId() . " 
                and qp1_cidade.ID = qp1_endereco.CIDADE_ID
                and qp1_endereco.TIPO = 'PRINCIPAL'
                and qp1_cliente.tree_left >= " . $leftValueCliente . "
                and qp1_cliente.tree_right <= " . $rightValueCliente . "
                " . $sqlComp . "
                " . $sqlProduto . "
            group by 
                qp1_cliente.ID
             
            " . $sqlHaving . "
            order by
                qp1_cliente.created_at ASC";

    $stmt = $conSlave->prepare($SQL);
    $stmt->execute();

    $sqlContacts = "select nome,email FROM (
              select
                qp1_cliente.NOME as nome,
                qp1_cliente.EMAIL as email,
                (
                    SELECT
                        SUM(qp1_pedido_item.VALOR_UNITARIO * qp1_pedido_item.QUANTIDADE)
                    FROM
                        qp1_pedido,
                        qp1_pedido_item
                    WHERE
                        qp1_pedido.CLIENTE_ID = qp1_cliente.ID
                        and qp1_pedido.ID = qp1_pedido_item.PEDIDO_ID
                        AND qp1_pedido.STATUS <> 'CANCELADO'
                ) AS totalCompras
            from
                qp1_cliente
                LEFT JOIN qp1_pedido ON (
                  qp1_pedido.CLIENTE_ID = qp1_cliente.ID
                )
                LEFT JOIN qp1_pedido_item ON (
                  qp1_pedido.ID = qp1_pedido_item.PEDIDO_ID
                ),
                qp1_endereco,
                qp1_cidade
            where
                qp1_endereco.CLIENTE_ID = qp1_cliente.ID
                and qp1_cliente.ID <> " . $objClienteLogado->getId() . " 
                and qp1_cidade.ID = qp1_endereco.CIDADE_ID
                and qp1_endereco.TIPO = 'PRINCIPAL'
                and qp1_cliente.tree_left >= " . $leftValueCliente . "
                and qp1_cliente.tree_right <= " . $rightValueCliente . "
                " . $sqlComp . "
                " . $sqlProduto . "
            group by 
                qp1_cliente.ID
             
            " . $sqlHaving . "
            order by
                qp1_cliente.created_at ASC ) as t";

    $stmtAll = $conSlave->prepare($sqlContacts);
    $stmtAll->execute();

    $allContacts = $stmtAll->fetchAll(PDO::FETCH_ASSOC);



    //$pager = new QPropelPager($query, 'ClientePeer', 'doSelect', $page);
