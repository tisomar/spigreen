<?php


set_time_limit(0);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

ini_set('memory_limit', '1024M');
error_reporting(E_ALL);

set_time_limit(0);
ini_set('memory_limit', '-1');

$inicio = time();

$nl = ('cli' === php_sapi_name()) ? "\n" : "<br>";

$gerenciador = new GerenciadorClassificacaoUnilevel($con = Propel::getConnection());

$gerenciador->classificarClientes();

echo 'Finalizado com sucesso.', $nl;
echo 'Tempo: ', number_format(time() - $inicio, 0, '', '.'), ' (s)', $nl;
echo 'memoria: ', number_format(memory_get_peak_usage(), 0, '', '.');

exit(0);
