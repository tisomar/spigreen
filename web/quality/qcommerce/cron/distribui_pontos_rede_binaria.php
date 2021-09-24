<?php

set_time_limit(0);
ini_set('memory_limit', '-1');

$inicio = time();

$nl = ('cli' === php_sapi_name()) ? "\n" : "<br>";

//Primeiro procura alguma distribuicao aguardando preview
$distribuicao = DistribuicaoQuery::create()
                    ->filterByStatus(Distribuicao::STATUS_AGUARDANDO_PREVIEW)
                    ->filterByData(new DateTime(), Criteria::LESS_THAN)
                    ->orderByData()
                    ->findOne();

if ($distribuicao) {
    echo "Executando preview distribuição {$distribuicao->getId()} ...", $nl;
        
    $gerenciador = new GerenciadorBonusRedeBinaria(Propel::getConnection(), $logger);
    
    $gerenciador->geraPreview($distribuicao);
} else {
    //Nenhuma distribuicao aguardando preview. Verifica se existe alguma aguardando execução (confirmação).
    $distribuicao = DistribuicaoQuery::create()
                    ->filterByStatus(Distribuicao::STATUS_AGUARDANDO)
                    ->filterByData(new DateTime(), Criteria::LESS_THAN)
                    ->orderByData()
                    ->findOne();
    
    if ($distribuicao) {
        echo "Executando distribuição {$distribuicao->getId()} ...", $nl;
        
        $gerenciador = new GerenciadorBonusRedeBinaria(Propel::getConnection(), $logger);

        $gerenciador->confirmaDistribuicao($distribuicao);
    } else {
        echo "Nenhuma distribuição agendada para esta data.";
        exit(0);
    }
}

echo 'Finalizado com sucesso.', $nl;
echo 'Tempo: ', number_format(time() - $inicio, 0, '', '.'), ' (s)', $nl;
echo 'memoria: ', number_format(memory_get_peak_usage(), 0, '', '.');

exit(0);
