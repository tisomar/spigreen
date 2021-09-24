<?php
use Dompdf\Dompdf;
use Doctrine\Common\Collections\Criteria;
use PFBC\Element\DateTime;

$_class = 'Resgate';

$preQuery = ResgateQuery::create()
->useClienteQuery()
->endUse()
->orderByData(Criteria::DESC);


if(!empty($request->query->get('filter')) ) :
    $request->query->set('is_filter', true);
endif;

$classQueryName = $_classQuery = $_class . 'Query';
$object_peer    = $_class::PEER;
$query_builder  = $classQueryName::create(null, $preQuery);

include_once QCOMMERCE_DIR . '/admin/relatorio/actions/solicitacao-resgate/filter.basic.action.query.php';

if ($container->getRequest()->query->has('exportar')) {
    $objects = $query_builder->find();
    
    if (count($objects) == 0) {
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    }
    
    $content = 'Nome Solicitante;Cpf Solicitante;E-mail;Banco;Agência;Conta;Cpf Correntista;Valor;Data Solicitação;Status' . PHP_EOL;
    foreach ($objects as $dadoResgate) :

        $clienteNome = $dadoResgate->getCliente()->getNome();  
        $clienteCpf = $dadoResgate->getCliente()->getCpf();  
        $email = $dadoResgate->getCliente()->getEmail();  

        $row = [
            '"' . htmlspecialchars($clienteNome) . '"',
            '"' . $clienteCpf . '"',
            '"' . $email . '"',
            '"' . $dadoResgate->getBanco() . '"',
            '"' . $dadoResgate->getAgencia(). '"',
            '"' . $dadoResgate->getConta() . '"',
            '"' . $dadoResgate->getCpfCorrentista() . '"',
            '"' . 'R$ ' . number_format($dadoResgate->getValor(), '2', ',', ''). '"',
            '"' . $dadoResgate->getData('d/m/Y H:i:s') . '"',
            '"' . $dadoResgate->getSituacao() . '"'
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

foreach ($objects as $dadoResgate) : /* @var $object Resgate */
    // $aniversariante = $dadoResgate->getCliente();
    // $nomeAniversariante = $dadoResgate->getCliente()->getNome();
    // $clienteNome = $dadoResgate->getCliente()->getNome();
    // $clienteCpf = $dadoResgate->getCliente()->getCpf();
    // $clienteEmail = $dadoResgate->getCliente()->getEmail();
    // $clienteBanco = $dadoResgate->getBanco();
    // $clienteAgencia = $dadoResgate->getAgencia();
    // $clienteConta = $dadoResgate->getConta();
    // $clienteCorrentista = $dadoResgate->getNomeCorrentista();
    // $clienteCpfCorrentista = $dadoResgate->getCpfCorrentista();
    // $clienteCnpjCorrentista = $dadoResgate->getCnpjCorrentista();
    $clienteValor = number_format($dadoResgate->getValor(), '2', ',', '');
    // $clienteResgate = $dadoResgate->getData('d/m/Y');
    // $clienteStatus = $dadoResgate->getSituacao();
    // var_dump($dadoResgate);die;
    
    $data = date('d/m/Y');
    $dados .=
        "<tr>
            <td data-title='Nome'> {$dadoResgate->getCliente()->getNome()} </td>
            <td data-title='CPF'> {$dadoResgate->getCliente()->getCpf()} </td>
            <td data-title='Email'> {$dadoResgate->getCliente()->getEmail()} </td>
            <td data-title='Banco'> {$dadoResgate->getBanco()} </td>
            <td data-title='Agencia'> {$dadoResgate->getAgencia()} </td>
            <td data-title='Conta'> {$dadoResgate->getConta()} </td>
            <td data-title='Tipo Conta'> {$dadoResgate->getBanco()} </td>
            <td data-title='Nome Correntista'> {$dadoResgate->getNomeCorrentista()} </td>
            <td data-title='CPF Correntista'> {$dadoResgate->getCpfCorrentista()} </td>
            <td data-title='CNPJ Correntista'> {$dadoResgate->getCnpjCorrentista()} </td>
            <td data-title='Valor'> R$ {$clienteValor} </td>
            <td data-title='Data da Solicitação'>{$dadoResgate->getData('d/m/Y')} </td>
            <td data-title='Status'> R$ {$dadoResgate->getSituacao()} </td>
        </tr>";
endforeach;

$html = "
    <span style='text-align: right;'>Data geração: {$data}</span>
    <span  style='font-family:arial; text-align:center'> 
        <h2>Relatório de Solicitação de Resgate</h2><br>
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
                <th>CPF</th>
                <th>E-mail</th>
                <th>Banco</th>
                <th>Agência</th>
                <th>Conta</th>
                <th>Tipo da Conta</th>
                <th>Correntista</th>
                <th>CPF do Correntista</th>
                <th>CNPJ do Correntista</th>
                <th>Valor</th>
                <th>Data da Solicitação</th>
                <th>Status</th>
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
$dompdf->stream('relatorio_solicitacao_de_resgate.pdf');
exit();
endif;

$page = $request->query->get('page', 1);
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page, 30);
$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());

$listaBancos = ResgateQuery::create()
->useClienteQuery()
->endUse()
->orderByData(Criteria::DESC)
->find()
->toArray();
