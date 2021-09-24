<?php
use Dompdf\Dompdf;

$preQuery = TransferenciaQuery::create()->orderByData(Criteria::DESC);

$_class = 'Transferencia';
$classQueryName = $_classQuery = $_class . 'Query';
$object_peer    = $_class::PEER;
$query_builder  = $classQueryName::create(null, $preQuery);

include_once QCOMMERCE_DIR . '/admin/relatorio/actions/transferencia/filter.basic.action.query.php';

if(!empty($request->query->get('filter')) ) :
    $request->query->set('is_filter', true);
endif;

if ($container->getRequest()->query->has('exportar')) {
    $objects = $query_builder->find();
    
    if (count($objects) == 0) {
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    }
    
    $content = 'Cliente Remetente / Cod Patricinador;Cliente Destinatario / Cod Patricinador;Data Transferencia;Valor' . PHP_EOL;
    foreach ($objects as $object) :

        $clienteRemetente = ClientePeer::retrieveByPK($object->getClienteRemetenteId());
        $clienteDestinatario = ClientePeer::retrieveByPK($object->getClienteDestinatarioId());

        $row = [
            '"' . $clienteRemetente->getNomeCompleto() . ' / #' . $clienteRemetente->getChaveIndicacao() . '"',
            '"' . $clienteDestinatario->getNomeCompleto() . ' / #' . $clienteDestinatario->getChaveIndicacao() . '"',
            '"' . $object->getData('d/m/Y')  . '"',
            '"' . $object->getQuantidadePontos() . '"',
        ];
        $content .= implode(';', $row) . PHP_EOL;
    endforeach;
    // Pegando a codificação atual de $content (provavelmente UTF-8)
    $codificacaoAtual = mb_detect_encoding($content, 'auto', true);

    // Convertendo para a ISO-8859-1 para arrumar problemas de codificação em acentos
    $content = mb_convert_encoding($content, 'ISO-8859-1', $codificacaoAtual);

    // Criando nome para o arquivo
    $filename = sprintf('tranferencia_%s.csv', date('Y-m-d H-i-s'));

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
    foreach ($objects as $object) : /* @var $object DistribuicaoCliente */
        $clienteRemetente = ClientePeer::retrieveByPK($object->getClienteRemetenteId());
        $clienteDestinatario = ClientePeer::retrieveByPK($object->getClienteDestinatarioId());
        
        $data = date('d/m/Y');
        $dados .=
            "<tr>
                <td>
                    {$clienteRemetente->getNomeCompleto()} - #{$clienteRemetente->getChaveIndicacao()}
                </td>
                <td> 
                    {$clienteDestinatario->getNomeCompleto()} - #{$clienteDestinatario->getChaveIndicacao()}
                </td>
                <td>{$object->getData('d/m/Y')}</td>
                <td>{$object->getQuantidadePontos()}</td>
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
            <tr>
                <th>Cliente Remetente / Cod Patrocinador</th>
                <th>Cliente Destinatário / Cod Patrocinador</th>
                <th>Data Transferência</th>
                <th>Valor Transferido</th>
            </tr>
            $dados
        </table>";

    $dompdf->loadHtml($html);

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'landscape');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream('relatorio_transferencia.pdf');
    exit();
endif;

$page = $request->query->get('page', 1);
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page, 30);
$container->getSession()->set('last.page.' . $router->getModule(), $container->getRequest()->getRequestUri());
    
