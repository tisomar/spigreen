<?php

set_time_limit(0);
ini_set('memory_limit', '-1');

$inicio = time();

$nl = ('cli' === php_sapi_name()) ? "\n" : "<br>";

////Primeiro procura alguma participacao resultados aguardando preview
$participacaoResultado = ParticipacaoResultadoQuery::create()
                                ->filterByStatus(ParticipacaoResultado::STATUS_AGUARDANDO_PREVIEW)
                                ->filterByData(new DateTime(), Criteria::LESS_THAN)
                                ->filterByTipo(ParticipacaoResultado::TIPO_ACELERACAO)
                                ->orderByData()
                                ->findOne();

if ($participacaoResultado) {
    echo "Executando preview participação resultados (Bônus aceleração) {$participacaoResultado->getId()} ...", $nl;

    $gerenciador = new BonificacaoAceleracao($con = Propel::getConnection(), $logger);
    $gerenciador->geraPreview($participacaoResultado);

} else {

    //Nenhuma participacao bonus desempenho aguardando preview. Verifica se existe alguma aguardando execução (confirmação).
    $participacaoResultado = ParticipacaoResultadoQuery::create()
                                ->filterByStatus(ParticipacaoResultado::STATUS_AGUARDANDO)
                                ->filterByData(new DateTime(), Criteria::LESS_THAN)
                                ->filterByTipo(ParticipacaoResultado::TIPO_ACELERACAO)
                                ->orderByData()
                                ->findOne();
    if ($participacaoResultado) {
        echo "Executando participação resultados (Bônus desempenho) {$participacaoResultado->getId()} ...", $nl;
        
        $gerenciador = new BonificacaoAceleracao($con = Propel::getConnection(), $logger);
        $gerenciador->distribuirBonus($participacaoResultado);
    } else {
        echo "Nenhuma participação resultados (Bônus desempenho) agendada para esta data.";
        exit(0);
    }
}

echo 'Finalizado com sucesso.', $nl;
echo 'Tempo: ', number_format(time() - $inicio, 0, '', '.'), ' (s)', $nl;
echo 'memoria: ', number_format(memory_get_peak_usage(), 0, '', '.');

exit(0);
