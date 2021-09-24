<?php

/**
 * Exporta em um arquivo txt a lista de e-mails de usuários que cadastraram seus e-mails na tabela de newsletter.
 */

include(QCOMMERCE_DIR . '/admin/includes/config.inc.php');
include(QCOMMERCE_DIR . '/admin/includes/security.inc.php');

$query_builder = NewsletterQuery::create()
        ->select(array('Nome', 'Email', 'DataCadastro'))
        ->orderByEmail();

include_once QCOMMERCE_DIR . '/admin/_2015/actions/list/filter.basic.action.query.php';

$objects = $query_builder->find();

if (count($objects) == 0) {
    $session->getFlashBag()->set('info', 'A lista está vazia!');
    redirectTo(get_url_admin() . '/newsletter/list');
    exit;
}

//$pager = new QPropelPager($query_builder, $object_peer, 'doSelect', $page);
$content = str_replace(',', ';', $objects->toCSV());
        
// Pegando a codificação atual de $content (provavelmente UTF-8)
$codificacaoAtual = mb_detect_encoding($content, 'auto', true);

// Convertendo para a ISO-8859-1 para arrumar problemas de codificação em acentos
$content = mb_convert_encoding($content, 'ISO-8859-1', $codificacaoAtual);

// Criando nome para o arquivo
$filename = sprintf('newsletter_%s.csv', date('Y-m-d H-i'));

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
