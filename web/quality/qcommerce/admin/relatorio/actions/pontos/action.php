<?php
use Dompdf\Dompdf;

$_class = 'Extrato';

$classQueryName = $_classQuery = $_class . 'Query';
$object_peer    = $_class::PEER;

$preQuery = ExtratoQuery::create()
    ->filterByOperacao('+')
    ->orderByData();

$query_builder  = $classQueryName::create(null, $preQuery);

$filters = $request->query->get('filter');

include_once QCOMMERCE_DIR . '/admin/_2015/actions/list/filter.basic.action.query.php';

if ($container->getRequest()->query->has('exportar')) {
    $objects = $query_builder->find();
    
    if (count($objects) == 0) {
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    }
    
    $content = 'Data;Cliente;Tipo;Valor;Descricao' . PHP_EOL;
    foreach ($objects as $dadoPontos) :

        $clienteNome = $dadoPontos->getCliente()->getNome();  

        $descricao =  '';
        if ($dadoPontos->getPedido() !== null) :
            $nivel = $dadoPontos->getPedido()->getCliente()->getTreeLevel() - $dadoPontos->getCliente()->getTreeLevel();
            $descricao =  $dadoPontos->getObservacao() . ' (' . $nivel . 'º nível)';
        else:
            $descricao =  $dadoPontos->getObservacao();
        endif;

        $row = [
            '"' . $dadoPontos->getData('d/m/Y') . '"',
            '"' . $clienteNome . '"',
            '"' . $dadoPontos->getTipo() . '"',
            '"' . number_format($dadoPontos->getPontos(), 2, ',', '.') . '"',
            '"' . $descricao. '"',
        ];
        $content .= implode(';', $row) . PHP_EOL;
    endforeach;
    // Pegando a codificação atual de $content (provavelmente UTF-8)
    $codificacaoAtual = mb_detect_encoding($content, 'auto', true);

    // Convertendo para a ISO-8859-1 para arrumar problemas de codificação em acentos
    $content = mb_convert_encoding($content, 'ISO-8859-1', $codificacaoAtual);

    // Criando nome para o arquivo
    $filename = sprintf('clientes_descontinuados_%s.csv', date('Y-m-d H-i-s'));

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

// PDF
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

    foreach ($objects as $dadoPontos) : /* @var $object Extrato */

        $descricao =  '';
        if ($dadoPontos->getPedido() !== null) :
            $nivel = $dadoPontos->getPedido()->getCliente()->getTreeLevel() - $dadoPontos->getCliente()->getTreeLevel();
            $descricao =  $dadoPontos->getObservacao() . ' (' . $nivel . 'º nível)';
        else:
            $descricao =  $dadoPontos->getObservacao();
        endif;

        $bonus = number_format($dadoPontos->getPontos(), 2, ',', '.');
        
        $data = date('d/m/Y');
        $dados .=
            "<tr>
                <td data-title='Data'> {$dadoPontos->getData('d-m-Y')} </td>
                <td data-title='Cliente'> {$dadoPontos->getCliente()->getNome()} </td>
                <td data-title='Tipo'> {$dadoPontos->getTipo()} </td>
                <td data-title='Pontos'> R$ {$bonus} </td>
                <td data-title='Descricao'> {$descricao} </td>
            </tr>";
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
                    <th>Data</th>
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th>Bônus</th>
                    <th>Descrição</th>
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
    $dompdf->stream('relatorio_bonus.pdf');
    exit();
endif;

$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());

$page = $request->query->get('page', 1);
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page, 30);
$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());
// $pager = $query_builder->find();

$listaClientes = ClienteQuery::create()
    ->orderByNome()
    ->find()
    ->toArray();

$tipoBonusQuery = ExtratoQuery::create()
    ->distinct()
    ->select([ExtratoPeer::TIPO])
    ->filterByTipo(Extrato::TIPO_SISTEMA, Criteria::NOT_EQUAL)
    ->filterByOperacao('+')
    ->orderByTipo()
    ->find();

$tipoBonus = [
    '' => ''
];

foreach ($tipoBonusQuery as $tipo) :
    if (isset(Extrato::$tiposDesc[$tipo])) :
        $tipoBonus[$tipo] = Extrato::$tiposDesc[$tipo];
    endif;
endforeach;

asort($tipoBonus);
