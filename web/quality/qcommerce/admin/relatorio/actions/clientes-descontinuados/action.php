<?php
use Dompdf\Dompdf;
use Doctrine\Common\Collections\Criteria;
use PFBC\Element\DateTime;

$_class = 'ClienteInativado';

$preQuery = ClienteInativadoQuery::create()
->useClienteRelatedByClienteIdQuery()
    ->filterByVago(true)
->endUse()
->orderByCreatedAt(Criteria::DESC);

if(!empty($_GET['filter']['DataDe']) && !empty($_GET['filter']['DataAte']) ) :
    $dataDe = date_create_from_format('d/m/Y', $_GET['filter']['DataDe']);
    $dataAte = date_create_from_format('d/m/Y', $_GET['filter']['DataAte']);
    $preQuery->filterByCreatedAt(['min' => $dataDe , 'max' => $dataAte]);
endif;

$classQueryName = $_classQuery = $_class . 'Query';
$object_peer    = $_class::PEER;


$query_builder  = $classQueryName::create(null, $preQuery);

include_once QCOMMERCE_DIR . '/admin/relatorio/actions/clientes-com-plano/filter.basic.action.query.php';

if ($container->getRequest()->query->has('exportar')) {

    $objects = $query_builder->find();

    if (count($objects) == 0) {
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    }

    $content = 'Nome;Cadastro Vago;E-mail;Telefone;Data Inativação' . PHP_EOL;

    foreach ($objects as $clienteInativo) :
        $cliente = $clienteInativo->getClienteRelatedByClienteId();  

        $row = [
            '"' . htmlspecialchars($clienteInativo->getNome()) . '"',
            '"' . $cliente->getNome() . '"',
            '"' . $clienteInativo->getEmail() . '"',
            '"' . $clienteInativo->getTelefone() . '"',
            '"' . $clienteInativo->getCreatedAt('d/m/Y H:i:s') . '"'
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

foreach ($objects as $clienteInativo) : /* @var $object ClienteInativado */
    // $clienteNome = $clienteInativo->getNome();
    // $dataEmail = $clienteInativo->getEmail();
    // $telefone = $clienteInativo->getTelefone();
    // $inativacao = $clienteInativo->getCreatedAt('d/m/Y H:i:s');
    // $cliente = $clienteInativo->getNome();
    // $nomeAniversariante = $clienteInativo->getCliente()->getNome();
    // var_dump($inativacao);die;
    
    $cliente = $clienteInativo->getClienteRelatedByClienteId();
    $clienteTipo = ClientePeer::getTipoCliente($cliente->getId());
    $ativacao = $clienteInativo->getDataAtivacao('d/m/Y H:i:s');
    
    $data = date('d/m/Y');
    $dados .=
        "<tr>
            <td data-title='Nome'> {$clienteInativo->getNome()} </td>
            <td data-title='Cadastro Vago'> {$cliente->getNome()} </td>
            <td data-title='Tipo'> {$clienteTipo} </td>
            <td data-title='Email'> {$clienteInativo->getEmail()} </td>
            <td data-title='Telefone'> {$clienteInativo->getTelefone()} </td>
            <td data-title='Data Inativação'> {$clienteInativo->getCreatedAt('d/m/Y H:i:s')} </td>
        </tr>";
endforeach;

$html = "
    <span style='text-align: right;'>Data geração: {$data}</span>
    <span  style='font-family:arial; text-align:center'> 
        <h2>Relatório de Clientes Descontinuados</h2><br>
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
                <th>Cadastro Vago</th>
                <th>Tipo</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Data da Inativação</th>
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
$dompdf->stream('relatorio_clientes_descontinuados.pdf');
exit();
endif;

$page = $request->query->get('page', 1);
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page, 30);

$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());

$listaClientes = ClienteInativadoQuery::create()
    ->useClienteRelatedByClienteIdQuery()
        ->filterByVago(true)
    ->endUse()
    ->orderByCreatedAt(Criteria::DESC)
    ->find()
    ->toArray();