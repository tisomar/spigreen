<?php
$param = array();
parse_str($request->server->get('QUERY_STRING'), $param);
if (isset($param['page'])) {
    unset($param['page']);
}
$url = http_build_query($param);
?>

<a href="<?php echo get_url_admin() ?>/bonus-produtos-preview/list/?exportar=1&<?php echo $url ?>" class="btn btn-primary export-newsletter">
    <i class="icon-cloud-download"></i> Excel
</a>

<a href="<?php echo get_url_admin() ?>/bonus-produtos-preview/list/?pdf=1&<?php echo $url ?>" class="btn btn-primary export-newsletter">
    <i class="icon-cloud-download"></i> PDF
</a>