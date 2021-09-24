<?php

$resgates = $_POST['resgates'] ?? null;

if (is_null($resgates)) :
    die;
endif;

$query = 
"SELECT 
    VALOR, 
    VALOR_TAXA, 
    DATA, 
    BANCO, 
    AGENCIA,
    CONTA,
    TIPO_CONTA,
    PIS_PASEP,
    NOME_CORRENTISTA,
    CPF_CORRENTISTA,
    SITUACAO 
FROM 
    qp1_resgate
WHERE 
    ID = $resgates";

$con = Propel::getConnection();
$stmt = $con->prepare($query);
$rs = $stmt->execute();

$teste = [];
while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) {
    $teste = $rs;
    $valor = $rs->VALOR;
    $valor_taxa = $rs->VALOR_TAXA;
    $data = $rs->DATA;
    $banco = $rs->BANCO;
    $agencia = $rs->AGENCIA;
    $conta = $rs->CONTA;
    $tipo_conta = $rs->TIPO_CONTA;
    $pis_pasep = $rs->PIS_PASEP;
    $nome_correntista = $rs->NOME_CORRENTISTA;
    $cpf_correntista = $rs->CPF_CORRENTISTA;
    $situacao = $rs->SITUACAO;
}

echo json_encode($teste);

// echo json_encode([
//     'valor' => $valor, 
//     'taxa' => $valor_taxa, 
//     'data' => $data, 
//     'banco' => $banco, 
//     'agencia' => $agencia,
//     'conta' => $conta,
//     'tipo-conta' => $tipo_conta,
//     'pis-pasep' => $pis_pasep,
//     'nome-correntista' => $nome_correntista,
//     'cpf-correntista' => $cpf_correntista,
//     'situacao' => $situacao,
//     ]);
