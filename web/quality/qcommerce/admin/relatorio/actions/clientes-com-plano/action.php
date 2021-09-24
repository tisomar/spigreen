<?php
use Dompdf\Dompdf;

$_class = 'Cliente';

$classQueryName = $_classQuery = $_class . 'Query';

$object_peer    = $_class::PEER;

$preQuery = ClienteQuery::create()
    ->orderByNome();
$query_builder  = $classQueryName::create(null, $preQuery);

include_once QCOMMERCE_DIR . '/admin/relatorio/actions/clientes-com-plano/filter.basic.action.query.php';

if ($container->getRequest()->query->has('exportar')) {
    // $query_builder
    //     ->select(array('Nome', 'Telefone', 'Email', 'PlanoId', 'vencimentoMensalidade'))
    // ;
    
    $objects = $query_builder->find();
    // var_dump($objects);
    if (count($objects) == 0) {
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    }
    
    $content = 'Nome;Telefone;E-mail;Plano;Data de Cadastro' . PHP_EOL;
    
    foreach ($objects as $cliente) :
        $plano = $cliente->getPlano() ? $cliente->getPlano()->getNome() : '-';
        $row = [
            '"' . $cliente->getNome() . '"',
            '"' . $cliente->getTelefone() . '"',
            '"' . $cliente->getEmail() . '"',
            '"' . $plano . '"',
            '"' . $cliente->getClienteDataCadastro() . '"'
        ];
        $content .= implode(';', $row) . PHP_EOL;
    endforeach;
    
    // $content = str_replace(',', ';', $objects->toCSV());
    
    // Pegando a codificação atual de $content (provavelmente UTF-8)
    $codificacaoAtual = mb_detect_encoding($content, 'auto', true);
    
    // Convertendo para a ISO-8859-1 para arrumar problemas de codificação em acentos
    $content = mb_convert_encoding($content, 'ISO-8859-1', $codificacaoAtual);
    
    // Criando nome para o arquivo
    $filename = sprintf('clientes_com_plano_%s.csv', date('Y-m-d H-i-s'));
    
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
foreach ($objects as $cliente) : /* @var $object Cliente */
    // $aniversariante = $cliente->getCliente();
    // $nomeAniversariante = $cliente->getCliente()->getNome();
    // var_dump($objects);die;
    $plano = $cliente->getPlano() ? $cliente->getPlano()->getNome() : '-';
    
    $data = date('d/m/Y');
    $dados .=
        "<tr>
            <td data-title='Nome'> {$cliente->getNome()} </td>
            <td data-title='Telefone'> {$cliente->getTelefone()} </td>
            <td data-title='Email'> {$cliente->getEmail()} </td>
            <td data-title='Plano'> {$plano} </td>
            <td data-title='Data de Cadastro'> {$cliente->getClienteDataCadastro()} </td>
        </tr>";
endforeach;
$html = "
    <span style='text-align: right;'>Data geração: {$data}</span>
    <span  style='font-family:arial; text-align:center'> 
        <h2>Relatório de Aniversariantes</h2>

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
                <th>Nome</th>
                <th>Telefone</th>
                <th>E-mail</th>
                <th>Plano</th>
                <th>Data de Cadastro</th>
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
$dompdf->stream('relatorio_clientes_com_plano.pdf');
exit();
endif;

$page = $request->query->get('page', 1);

$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page, 50);

$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());