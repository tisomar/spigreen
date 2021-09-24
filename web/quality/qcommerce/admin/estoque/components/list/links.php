<?php
$add = new \PFBC\Element\AddNewButton($config['routes']['registration']);
$add->render();

$sql = urlencode(http_build_query($request->query->get('filter')));

?>
<a target="_self" style="margin-left: 10px" class="btn btn-default"
   href="<?php echo get_url_site(); ?>/admin/relatorio/controle-estoque/?sql_estoque=<?php echo $sql ?>">Imprimir</a>

