<?php
// reference the Dompdf namespace
use Dompdf\Dompdf;

if (!isset($_class)) {
    trigger_error('você deve definir a classe $_class');
}

$classQueryName = $_class . 'Query';

if (!isset($preQuery)) {
    $preQuery = null;
}

$object_peer = $_class::PEER;
$query_builder = $classQueryName::create(null, $preQuery)->filterByOperacao('+');

// include_once QCOMMERCE_DIR . '/admin/' . $router->getModule() . '/actions/' . $router->getAction() . '/filter.basic.action.php';

if ($request->query->get('is_filter') == 'true') :

    $query_builder = ExtratoBonusProdutosQuery::create()
        ->useClienteQuery()
            ->filterByNome('%' . $request->query->get('filter')['NomeCliente'] . '%')
        ->endUse()
        ->filterByDistribuicaoId($request->query->get('distribuicao_id'))
        ->filterByOperacao('+');
endif;

if ($container->getRequest()->query->has('exportar')) {
    $objects =$query_builder->find();

    if ($request->query->get('is_filter') == 'true') :

        $objects   = ExtratoBonusProdutosQuery::create()
            ->useClienteQuery()
                ->filterByNome('%' . $request->query->get('filter')['NomeCliente'] . '%')
            ->endUse()
            ->filterByDistribuicaoId($request->query->get('distribuicao_id'))
            ->filterByOperacao('+')
            ->find();
    endif;


    if (count($objects) == 0) {
        $session->getFlashBag()->set('info', 'Não há nenhum registro para ser gerado.');
        redirectTo($container->getRequest()->server->get('HTTP_REFERER'));
        exit;
    }
    
    $content = 'Cliente;Graduacao;Telefone;Endereço;Descricao;Valor Total;Data retirada' . PHP_EOL;
    foreach ($objects as $dados) :
        $clienteNome = $dados->getCliente()->getNomeCompleto();  

        $cep = $dados->getCliente()->getEnderecoPrincipal()->getCep();
        $logradouro = $dados->getCliente()->getEnderecoPrincipal()->getLogradouro();
        $numero = $dados->getCliente()->getEnderecoPrincipal()->getNumero();
        $bairro = $dados->getCliente()->getEnderecoPrincipal()->getBairro();
        $cidade = $dados->getCliente()->getEnderecoPrincipal()->getCidade()->getNome();
        $estadoNome = $dados->getCliente()->getEnderecoPrincipal()->getCidade()->getEstado()->getNome();
        $estadoCigla = $dados->getCliente()->getEnderecoPrincipal()->getCidade()->getEstado()->getSigla();
        $complemento = $dados->getCliente()->getEnderecoPrincipal()->getComplemento() != null ? '<br>Complemento: ' . $dados->getCliente()->getEnderecoPrincipal()->getComplemento() : '';
        $dataRetirada = $dados->getDataRetirada() != null ? date('d/m/Y', strtotime($dados->getDataRetirada())) : '';

        $row = [
            '"' . htmlspecialchars($clienteNome) . '"',
            '"' . $dados->getGraduacao() . '"',
            '"' . $dados->getCliente()->getTelefone() . '"',
            '"' . 
            'CEP: ' . $cep .
            ' | Estado: ' .  $estadoNome . '/' .  $estadoCigla .
            ' | Cidade: ' . $cidade . 
            " | Logradouro: " . $logradouro .
            ' | Bairro: ' . $bairro .
            ' | Numero: ' .  $numero .
            $complemento . 
            '"',
            '"' . $dados->getObservacao() . '"',
            '"' . $dados->getValorTotalBonificacao() . '"',
            '"' .  $dataRetirada . '"',
        ];
        $content .= implode(';', $row) . PHP_EOL;
    endforeach;

    // Pegando a codificação atual de $content (provavelmente UTF-8)
    $codificacaoAtual = mb_detect_encoding($content, 'auto', true);

    // Convertendo para a ISO-8859-1 para arrumar problemas de codificação em acentos
    $content = mb_convert_encoding($content, 'ISO-8859-1', $codificacaoAtual);

    // Criando nome para o arquivo
    $filename = sprintf('bonus_produtos_%s.csv', date('Y-m-d H-i-s'));

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
    $objects =$query_builder->find();

    // instantiate and use the dompdf class
    $dompdf = new Dompdf();

    $dados = '';
    $data = '';
    foreach ($objects as $object) : /* @var $object DistribuicaoCliente */
        $data = date('d/m/Y', strtotime($object->getData()));
        
        $cep = $object->getCliente()->getEnderecoPrincipal()->getCep();
        $logradouro = $object->getCliente()->getEnderecoPrincipal()->getLogradouro();
        $numero = $object->getCliente()->getEnderecoPrincipal()->getNumero();
        $bairro = $object->getCliente()->getEnderecoPrincipal()->getBairro();
        $cidade = $object->getCliente()->getEnderecoPrincipal()->getCidade()->getNome();
        $estadoNome = $object->getCliente()->getEnderecoPrincipal()->getCidade()->getEstado()->getNome();
        $estadoCigla = $object->getCliente()->getEnderecoPrincipal()->getCidade()->getEstado()->getSigla();
        $complemento = $object->getCliente()->getEnderecoPrincipal()->getComplemento() != null ? '<br>Complemento: ' . $object->getCliente()->getEnderecoPrincipal()->getComplemento() : '';

        $dataRetirada = $object->getDataRetirada() != null ? date('d/m/Y', strtotime($object->getDataRetirada())) : '';
        $descricao = $object->getObservacao();
        $total = 'R$' . number_format($object->getValorTotalBonificacao(), 2, ',', '.');

        $dados .=
            "<tr>
                <td data-title='Nome'> {$object->getCliente()->getNomeCompleto()} </td>
                <td data-title='Graduacao'>{$object->getGraduacao()} </td>
                <td data-title='Telefone'> {$object->getCliente()->getTelefone()} </td>
                <td data-title='Endereço'> 
                    CEP: $cep
                    <br>Estado:  $estadoNome/$estadoCigla
                    <br>Cidade: $cidade 
                    <br>Logradouro: $logradouro
                    <br>Bairro: $bairro
                    <br>Numero: $numero
                    $complemento
                </td>
                <td data-title='Descricao'>{$descricao} </td>
                <td data-title='Total'>{$total} </td>
                <td data-title='DataRetirada'>{$dataRetirada} </td>
            </tr>";
    endforeach;
    
    $html = "
        <span style='text-align: right;'>Data geração: {$data}</span>
        <span  style='font-family:arial; text-align:center'> 
            <h2>Preview distribuíçao bônus produtos</h2><br>
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
                    <th>Cliente</th>
                    <th>Graduação</th>
                    <th>Telefone</th>
                    <th>Endereço</th>
                    <th>Descrição</th>
                    <th>Total</th>
                    <th>Data retirada</th>
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
    $dompdf->stream('preview_distribuicao_bonus_produtos.pdf');
    exit();
endif;

$page = $request->query->get('page') ? $request->query->get('page') : 1;
$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page);
