<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 16/04/2018
 * Time: 16:12
 */



if (isset($_GET['uf'])) {
    $estado = EstadoQuery::create()->filterBySigla($_GET['uf'])->findOne();

    if (!$estado instanceof Estado || $estado->getId() > 27) {
        die(var_dump('UF inválida;'));
    }
} else {
    die(var_dump('UF não informada;'));
}

$distribuicao = DistribuicaoQuery::create()->filterByStatus('DISTRIBUIDO')->orderById(Criteria::DESC)->findOne();

$arrClienteDistribuidor = ClienteQuery::create()
        ->filterByTreeLeft(0, Criteria::GREATER_THAN)
//        ->filterByVip(true)
        ->where(' qp1_distribuicao_preview.DISTRIBUICAO_ID = ' . $distribuicao->getId())
        ->addJoin('qp1_cliente.ID', 'qp1_distribuicao_preview.CLIENTE_ID', Criteria::INNER_JOIN)
        ->useEnderecoQuery()
            ->filterByTipo('PRINCIPAL')
            ->useCidadeQuery()
                ->filterByEstadoId($estado->getId())
            ->endUse()
        ->endUse()
        ->withColumn('qp1_cliente.ID ', 'ID_CLIENTE')
        ->withColumn('concat(NOME_RAZAO_SOCIAL, " ", SOBRENOME_NOME_FANTASIA)', 'NOME_COMPLETO')
        ->withColumn('qp1_cli
        
        
        
        
        
        ente.EMAIL', 'EMAIL_CLIENTE')
        ->withColumn('qp1_cliente.CPF_CNPJ', 'CPF_CNPJ_CLIENTE')
        ->withColumn('qp1_endereco.TELEFONE1 ', 'TELEFONE')
        ->withColumn('CASE WHEN qp1_cliente.VIP = 1 THEN "Sim" ELSE "Não" END ', 'VIP_CLIENTE')
        ->withColumn('qp1_distribuicao_preview.NIVEL_ATINGIDO ', 'NIVEL_ATINGIDO')
        ->withColumn('qp1_distribuicao_preview.PONTOS_REDE ', 'PONTOS_REDE')
        ->select(array('ID_CLIENTE','NOME_COMPLETO','CPF_CNPJ_CLIENTE', 'EMAIL_CLIENTE', 'VIP_CLIENTE', 'TELEFONE', 'NIVEL_ATINGIDO', 'PONTOS_REDE'))
        ->find();

$collection = new PropelArrayCollection();
$validate = false;

foreach ($arrClienteDistribuidor as $item) {
    $validate = true;
    $item = preg_replace('/,/', ':::', $item);
    $collection->append($item);
}

$content .= str_replace(',', ';', $collection->toCSV());

if ($validate) {
    $codificacaoAtual = mb_detect_encoding($content, 'auto', true);

    // Convertendo para a ISO-8859-1 para arrumar problemas de codificação em acentos
    $content = mb_convert_encoding($content, 'ISO-8859-1', $codificacaoAtual);


    // Criando nome para o arquivo
    $filename = sprintf('relatorio_%s.csv', date('Y-m-d H-i-s'));

    // Definindo header de saída
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-Description: File Transfer');
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename={$filename}");
    header("Expires: 0");
    header("Pragma: public");

    // Enviando headers para o browser
    $fp = fopen('php://output', 'w');

    fwrite($fp, $content);

    fclose($fp);
}

exit();
