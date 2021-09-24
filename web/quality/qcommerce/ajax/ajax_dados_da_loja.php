<?php

$estadoLoja = $_POST['estadoLoja'] ?? null;

if (is_null($estadoLoja)) :
    die;
endif;

$query = 
"SELECT 
    NOME, 
    ENDERECO, 
    TELEFONE, 
    VALOR, 
    PRAZO,
    CENTRO_DISTRIBUICAO_ID
FROM 
    qp1_retirada_loja 
WHERE 
    ID = $estadoLoja";

$con = Propel::getConnection();
$stmt = $con->prepare($query);
$rs = $stmt->execute();

while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) {
    $nome = $rs->NOME;
    $endereco = $rs->ENDERECO;
    $telefone = $rs->TELEFONE;
    $valor = formata_valor($rs->VALOR);
    $prazo = $rs->PRAZO;
    $centroDistribuicaoId = $rs->CENTRO_DISTRIBUICAO_ID;
}

echo json_encode(['nome' => $nome, 'endereco' => $endereco, 'telefone' => $telefone, 'valor' => $valor, 'prazo' => $prazo, 'centroDistribuicaoId' => $centroDistribuicaoId]);
