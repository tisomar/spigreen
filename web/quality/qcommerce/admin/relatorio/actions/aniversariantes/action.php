<?php
use Dompdf\Dompdf;

$_class = 'Cliente';

$classQueryName = $_classQuery = $_class . 'Query';
$object_peer    = $_class::PEER;

$preQuery = ClienteQuery::create()
    ->withColumn('DATE_FORMAT(Cliente.DataNascimento,"%d/%m")', 'Aniversario')
    ->orderBy('Aniversario')
    ->orderByNome();

$query_builder  = $classQueryName::create(null, $preQuery);

if (!array_key_exists('MesAniversario', $request->query->get('filter', array()))) {
    $request->query->add(array('filter' => array('MesAniversario' => date('n'))));
    $request->query->add(array('is_filter' => true));
}

include_once QCOMMERCE_DIR . '/admin/_2015/actions/list/filter.basic.action.query.php';

if ($container->getRequest()->query->has('exportar')) {
    $query_builder
        //->withColumn("CONCAT(TIMESTAMPDIFF(YEAR, Cliente.DataNascimento, last_day(date(concat_ws('-', YEAR(CURRENT_DATE()), MONTH(CURRENT_DATE()), 1)))), ' anos')", 'Idade')
        ->select(array('Nome', 'Telefone', 'Email', 'Aniversario'/*, 'Idade'*/))
    ;

    $objects = $query_builder->find();

    if (count($objects) == 0) {
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    }

    $content = str_replace(',', ';', $objects->toCSV());

    // Pegando a codificação atual de $content (provavelmente UTF-8)
    $codificacaoAtual = mb_detect_encoding($content, 'auto', true);

    // Convertendo para a ISO-8859-1 para arrumar problemas de codificação em acentos
    $content = mb_convert_encoding($content, 'ISO-8859-1', $codificacaoAtual);

    // Criando nome para o arquivo
    $filename = sprintf('aniversariantes_%s.csv', date('Y-m-d H-i-s'));

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

// PDF Export
if ($container->getRequest()->query->has('pdf')) :
    // $query_builder->select(array('Nome', 'Telefone', 'Email', 'Aniversario'/*, 'Idade'*/));
    
    $objects = $query_builder->find();
    // var_dump($objects);
       
    // instantiate and use the dompdf class
    $dompdf = new Dompdf();

    if (count($objects) == 0) :
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    endif;

    $dados = '';
    $data = '';

    foreach ($objects as $dadoClientes) : /* @var $object Cliente */
        // $aniversariante = $dadoClientes->getCliente();
        // $nomeAniversariante = $dadoClientes->getCliente()->getNome();
        // var_dump($objects);die;
        
        $data = date('d/m/Y');
        $dados .=
            "<tr>
                <td data-title='Aniversario'> {$dadoClientes->getDataNascimento('d/m')} </td>
                <td data-title='Nome'> {$dadoClientes->getNome()} </td>
                <td data-title='Email'> {$dadoClientes->getEmail()} </td>
                <td data-title='Telefone'> {$dadoClientes->getTelefone()} </td>
            </tr>";
    endforeach;
    
    $html = "
        <span style='text-align: right;'>Data geração: {$data}</span>
        <span  style='font-family:arial; text-align:center'> 
            <h2>Relatório de Aniversariantes</h2><br>
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
                    <th>Data de aniversário</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
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
    $dompdf->stream('relatorio_aniversariantes.pdf');
    exit();
endif;

$page = $request->query->get('page', 1);
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page, 50);

$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());
