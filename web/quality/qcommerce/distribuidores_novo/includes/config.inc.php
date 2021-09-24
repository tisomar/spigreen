<?php
QPTranslator::setLocale('pt');

//Qtde de Caracteres para Envio de Mensagem SMS
$configSmsLimitCaracteres = 140;

$images = array(
    'Sem Nível' => 'n0',
    '1o. Nível' => 'n1',
    '2o. Nível' => 'n2',
    '3o. Nível' => 'n3',
    '4o. Nível' => 'n4',
    '5o. Nível' => 'n5',
    '6o. Nível' => 'n6',
    '7o. Nível' => 'n7',
    'Diplomata' => 'diplomata',
    'Bronze' => 'bronze',
    'Prata' => 'prata',
    'Ouro' => 'ouro',
    'Platina' => 'platina',
    'Rubi' => 'rubi',
    'Safira' => 'safira',
    'Diamante' => 'diamante',
    'Duplo Diamante' => 'duplodiamante',
    'Triplo Diamante' => 'triplodiamante',
    'Diretor' => 'diretor',
    'Diretor 2 Estrelas' => 'diretor2estrelas',
    'Diretor 3 Estrelas' => 'diretor3estrelas',
    'Diretor 4 Estrelas' => 'diretor4estrelas',
    'Diretor 5 Estrelas' => 'diretor5estrelas',
    'Diretor 6 Estrelas' => 'diretor6estrelas',
    'Diretor 7 Estrelas' => 'diretor7estrelas',
    'Diretor 8 Estrelas' => 'diretor8estrelas',
    'Diretor 9 Estrelas' => 'diretor9estrelas',
    'Diretor 10 Estrelas' => 'diretor10estrelas',
    'Barão' => 'barao'
);

$niveis = array(
    '1o. Nível' => '1o. Nível',
    '2o. Nível' => '2o. Nível',
    '3o. Nível' => '3o. Nível',
    '4o. Nível' => '4o. Nível',
    '5o. Nível' => '5o. Nível',
    '6o. Nível' => '6o. Nível',
    '7o. Nível' => '7o. Nível',
    'Diplomata' => 'Diplomata',
    'Bronze' => 'Bronze',
    'Prata' => 'Prata',
    'Ouro' => 'Ouro',
    'Platina' => 'Platina',
    'Rubi' => 'Rubi',
    'Safira' => 'Safira',
    'Diamante' => 'Diamante',
    'Duplo Diamante' => 'Duplo Diamante',
    'Triplo Diamante' => 'Triplo Diamante',
    'Diretor' => 'Diretor',
    'Diretor 2 Estrelas' => 'Diretor 2 Estrelas',
    'Diretor 3 Estrelas' => 'Diretor 3 Estrelas',
    'Diretor 4 Estrelas' => 'Diretor 4 Estrelas',
    'Diretor 5 Estrelas' => 'Diretor 5 Estrelas',
    'Diretor 6 Estrelas' => 'Diretor 6 Estrelas',
    'Diretor 7 Estrelas' => 'Diretor 7 Estrelas',
    'Diretor 8 Estrelas' => 'Diretor 8 Estrelas',
    'Diretor 9 Estrelas' => 'Diretor 9 Estrelas',
    'Diretor 10 Estrelas' => 'Diretor 10 Estrelas',
    'Barão' => 'Barão'

);


/* @var $user Cliente */
$user = ClientePeer::getClienteLogado();

/* @var $endereco Endereco */
$endereco = $user->getEnderecoPrincipal();

$distribuicao = DistribuicaoQuery::create()
    ->filterByStatus(Distribuicao::STATUS_DISTRIBUIDO)
    ->orderById(Criteria::DESC)
    ->findOne();

/* @var $distribuicaoPreview DistribuicaoCliente */
$distribuicaoPreview = DistribuicaoClienteQuery::create()
    ->filterByClienteId($user->getId())
    ->filterByDistribuicaoId($distribuicao)
    ->findOne();

if ($distribuicaoPreview) {
    $nivel = "Nivel 1";

    if ($nivel == '-') {
        $nivel = 'agenda.sem_nivel';
    }
} else {
    $nivel = 'agenda.sem_nivel';
}
