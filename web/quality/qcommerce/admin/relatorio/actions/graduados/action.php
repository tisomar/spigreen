<?php

use Dompdf\Dompdf;

$_class = 'PlanoCarreiraHistorico';

$classQueryName = $_classQuery = $_class . 'Query';
$object_peer = $_class::PEER;

//$preQuery = $classQueryName::create()
//    ->usePlanoCarreiraQuery()
//        ->orderByNivel(Criteria::DESC)
//    ->endUse();

$preQuery = $classQueryName::create()
    ->usePlanoCarreiraQuery()
    ->orderByNivel(Criteria::DESC)
    ->endUse()
    ->useClienteQuery()
    ->useEnderecoQuery()
    ->useCidadeQuery()
    ->endUse()
    ->endUse()
    ->endUse()
    ->groupByClienteId()
    ->withColumn(CidadePeer::NOME, 'NOME_CIDADE');
//var_dump($preQuery);exit();

$query_builder = $classQueryName::create(null, $preQuery);

$filters = $request->query->get('filter');

include_once QCOMMERCE_DIR . '/admin/_2015/actions/list/filter.basic.action.query.php';
// var_dump($container->getRequest()->query->has('exportar'));

$meses = [
    '' => 'Selecione o mês',
    '1' => 'Janeiro',
    '2' => 'Fevereiro',
    '3' => 'Março',
    '4' => 'Abril',
    '5' => 'Maio',
    '6' => 'Junho',
    '7' => 'Julho',
    '8' => 'Agosto',
    '9' => 'Setembro',
    '10' => 'Outubro',
    '11' => 'Novembro',
    '12' => 'Dezembro'
];

$anos = ['' => 'Selecione o ano'];

if ($container->getRequest()->query->has('exportar')) {
    $objects = $query_builder->find();

    if (count($objects) == 0) {
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    }

    //$content = 'Cliente;Graduacao;Pontos;Mes;Ano;Geracao;CodigoDoPatrocinador;ProximaGraduacao;Cidade' . PHP_EOL;
    $content = 'Cliente;Graduacao;Pontos;Mes;Ano;Geracao;CodigoDoPatrocinador;ProximaGraduacao' . PHP_EOL;
    foreach ($objects as $dadoClientes) :

        // $clienteNome = $dadoClientes->getCliente()->getNome();
        $cliente = $dadoClientes->getCliente();
        $planoCarreira = $dadoClientes->getPlanoCarreira();

        $proximaGraduacao = PlanoCarreiraQuery::create()
            ->filterByNivel($planoCarreira->getNivel() + 1)
            ->findOne();

        if (empty($proximaGraduacao)) :
            $strProximaGraduacao = 'Graduação Máxima';
        else :
            $strProximaGraduacao = $proximaGraduacao->getGraduacao();
        endif;
        $geracao = $cliente->getTreeLevel() . 'º geração';
        $row = [
            '"' . utf8_encode($cliente->getNomeCompleto()) . '"',
            '"' . $planoCarreira->getGraduacao() . '"',
            '"' . $dadoClientes->getVolumeTotalGrupo() . '"',
            '"' . $meses[$dadoClientes->getMes()] . '"',
            '"' . $dadoClientes->getAno() . '"',
            '"' . utf8_encode($geracao) . '"',
            '"' . $cliente->getChaveIndicacao() . '"',
            '"' . $strProximaGraduacao . '"'
            //'"' . utf8_encode($cliente->getEnderecoPrincipal()->getCidade()->getNome()) . ' - ' . $cliente->getEnderecoPrincipal()->getCidade()->getEstado()->getSigla() . '"'
        ];
        $content .= implode(';', $row) . PHP_EOL;
        //var_dump($row);
    endforeach;
    // Pegando a codificação atual de $content (provavelmente UTF-8)
    $codificacaoAtual = mb_detect_encoding($content, 'auto', true);

    // Convertendo para a ISO-8859-1 para arrumar problemas de codificação em acentos
    $content = mb_convert_encoding($content, 'ISO-8859-1', $codificacaoAtual);

    // Criando nome para o arquivo
    $filename = sprintf('graduados_%s.csv', date('Y-m-d H-i-s'));

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
    exit();
}


if ($container->getRequest()->query->has('pdf')) :
    $objects = $query_builder->find();

    // instantiate and use the dompdf class
    $dompdf = new Dompdf();

    if (count($objects) == 0) :
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    endif;

    $dados = '';
    $data = '';

    foreach ($objects as $dadoClientes) : /* @var $object DistribuicaoCliente */
        $cliente = $dadoClientes->getCliente();
        $planoCarreira = $dadoClientes->getPlanoCarreira();

        $proximaGraduacao = PlanoCarreiraQuery::create()
            ->filterByNivel($planoCarreira->getNivel() + 1)
            ->findOne();

        if (empty($proximaGraduacao)) :
            $strProximaGraduacao = 'Graduação Máxima';
        else :
            $strProximaGraduacao = $proximaGraduacao->getGraduacao();
        endif;

        $data = date('d/m/Y');
        $dados .=
            "<tr>
                <td data-title='Nome Cliente'> {$cliente->getNomeCompleto()} </td>
                <td data-title='Graduação'> {$planoCarreira->getGraduacao()} </td>
                <td data-title='Pontos'> {$dadoClientes->getVolumeTotalGrupo()} </td>
                <td data-title='Mês'> {$dadoClientes->getMes()} </td>
                <td data-title='Ano'> {$dadoClientes->getAno()} </td>
                <td data-title='Geração'> {$cliente->getTreeLevel()} º geração </td>
                <td data-title='Código do Patrocinador'> {$cliente->getChaveIndicacao()} </td>
                <td data-title='Próxima Graduação'> {$strProximaGraduacao} </td>
            </tr>";
//        $dados .=
//            "<tr>
//                <td data-title='Nome Cliente'> {$cliente->getNomeCompleto()} </td>
//                <td data-title='Graduação'> {$planoCarreira->getGraduacao()} </td>
//                <td data-title='Pontos'> {$dadoClientes->getVolumeTotalGrupo()} </td>
//                <td data-title='Mês'> {$dadoClientes->getMes()} </td>
//                <td data-title='Ano'> {$dadoClientes->getAno()} </td>
//                <td data-title='Geração'> {$cliente->getTreeLevel()} º geração </td>
//                <td data-title='Código do Patrocinador'> {$cliente->getChaveIndicacao()} </td>
//                <td data-title='Próxima Graduação'> {$strProximaGraduacao} </td>
//                <td data-title='Cidade'>{$cliente->getEnderecoPrincipal()->getCidade()->getNome()} - {$cliente->getEnderecoPrincipal()->getCidade()->getEstado()->getSigla()}</td>
//            </tr>";
    endforeach;

    $html = "
        <span style='text-align: right;'>Data geração: {$data}</span>
        <span  style='font-family:arial; text-align:center'> 
            <h2>Relatório de Graduados</h2><br>
        </span>

        <style>
            table, th, td {
                border: 1px solid black;
                border-collapse: collapse;
            }
            th, td {
                padding: 5px;
                text-align: left;
            }
        </style>
            
        <table width='100%' cellpadding='0' cellspacing='0' border='0' class='table table-hover table-striped'>
            <thead>
                <tr>
                    <th>Nome Cliente</th>
                    <th>Graduação</th>
                    <th>Pontos</th>
                    <th>Mês</th>
                    <th>Ano</th>
                    <th>Geração</th>
                    <th>Código do Patrocinador</th>
                    <th>Próxima Graduação</th>
                    <th>Cidade</th>
                </tr>
            </thead>
            <tbody>
                $dados
            <tbody>
        </table>";

    $dompdf->loadHtml($html);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'landscape');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream('relatorio_graduados.pdf');
    exit();
endif;

$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());

$page = $request->query->get('page', 1);
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page, 30);
$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());
// $pager = $query_builder->find();


$anosQuery = ExtratoQuery::create()
    ->distinct()
    ->select(['ANO'])
    ->withColumn("year(DATA)", 'ANO')
    ->orderBy('ANO', Criteria::ASC)
    ->find()
    ->toArray();

foreach ($anosQuery as $ano) :
    $anos[$ano] = $ano;
endforeach;

$listaClientesQuery = ClienteQuery::create()
    ->select(['ID', 'NOME'])
    ->withColumn(ClientePeer::ID, 'ID')
    ->withColumn(
        sprintf(
            'IF(%s IS NOT NULL, %s, %s)',
            ClientePeer::CNPJ,
            ClientePeer::RAZAO_SOCIAL,
            ClientePeer::NOME
        ),
        'NOME'
    )
    ->filterByVago(0)
    ->addAscendingOrderByColumn('NOME')
    ->find()
    ->toArray();

$listaClientes = [
    '' => 'Selecione o cliente'
];

foreach ($listaClientesQuery as $cliente) :
    $listaClientes[$cliente['ID']] = $cliente['NOME'];
endforeach;

// $tipoBonusQuery = ExtratoQuery::create()
//     ->distinct()
//     ->select([ExtratoPeer::TIPO])
//     ->filterByTipo(Extrato::TIPO_SISTEMA, Criteria::NOT_EQUAL)
//     ->filterByOperacao('+')
//     ->orderByTipo()
//     ->find();

// $tipoBonus = [
//     '' => ''
// ];

// foreach ($tipoBonusQuery as $tipo) :
//     if (isset(Extrato::$tiposDesc[$tipo])) :
//         $tipoBonus[$tipo] = Extrato::$tiposDesc[$tipo];
//     endif;
// endforeach;

// asort($tipoBonus);
