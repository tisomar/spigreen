
<?php
$param = array();
parse_str($request->server->get('QUERY_STRING'), $param);
if (isset($param['page'])) {
    unset($param['page']);
}
$url = http_build_query($param);
?>

<a href="#" class="btn btn-default" onclick="javascript:window.print();">Imprimir</a>
<a href="<?php echo get_url_admin() ?>/relatorio/volume-faturamento/?exportar=1&<?php echo $url ?>" class="btn btn-primary export-newsletter">
    <i class="icon-cloud-download"></i> Exportar
</a>

<a href="<?php echo get_url_admin() ?>/relatorio/volume-faturamento/?pdf=1&<?php echo $url ?>" class="btn btn-primary export-newsletter">
    <i class="icon-cloud-download"></i> PDF
</a>