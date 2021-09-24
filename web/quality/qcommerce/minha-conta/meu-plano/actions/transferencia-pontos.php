<?php
$erros = array();
if ($request->request->has('transferencia_puntos')):
    $cliente = ClientePeer::getClienteLogado(true);

    $now = new DateTime('now');

    $gerenciador = new GerenciadorPontos($con = Propel::getConnection(), $logger);

    $totalPontosDisponiveis = $gerenciador->getTotalPontosDisponiveisParaResgate($cliente, null, null, Extrato::TIPO_INDICACAO_DIRETA, true);

    $data = $request->request->get('transferencia_puntos');

    $pontos = $data["QUANTIDADE"];

    $destinatario = ClienteQuery::create()->findByEmail($data["EMAIL"]);

    $transferencia = new Transferencia();
    $transferencia->setClienteRemetenteId($cliente->getId());
    $transferencia->setQuantidadePontos($pontos);

    $erros = [];

    if (count($destinatario) <= 0):
        $erros[] = "Franqueado não encontrado. Confira se o e-mail informado está correto.";
    else:
        $destinatario = $destinatario[0];
        $transferencia->setClienteDestinatarioId($destinatario->getId());

        if ($cliente->getId() == $destinatario->getId()):
            $erros[] = "Não pode se transferir para você mesmo.";
        elseif ($pontos <= 0):
            $erros[] = "O minimo de bônus é <strong>1</strong>.";
        elseif ($pontos > $totalPontosDisponiveis):
            $valorBonus = format_money($totalPontosDisponiveis);
            $erros[] = "O limite de bônus disponíveis é <strong>R$ {$valorBonus}</strong>.";
        endif;
    endif;

    if ($transferencia->myValidate($erros) && !$erros):
        $transferencia->save($con);

        $gerenciador->transferirPontos(
            $cliente,
            $destinatario,
            $pontos,
            $transferencia->getId()
        );

        FlashMsg::success('Transferência de bônus realizada com sucesso.');
    endif;
endif;

foreach ($erros as $erro):
    FlashMsg::danger($erro);
endforeach;

redirect('/minha-conta/meu-plano');
