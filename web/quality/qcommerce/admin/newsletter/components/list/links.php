<?php
$param = array();
parse_str($request->server->get('QUERY_STRING'), $param);
if (isset($param['page'])) {
    unset($param['page']);
}
$url = http_build_query($param);
?>

<a href="exportar<?php echo $url ?>" class="btn btn-primary export-newsletter">
    <i class="icon-cloud-download"></i> Exportar
</a>
