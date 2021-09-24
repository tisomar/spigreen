<?php
$param = array();

parse_str($request->server->get('QUERY_STRING'), $param);

if (isset($param['page'])) :
    unset($param['page']);
endif;

$url = http_build_query($param);