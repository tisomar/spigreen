<?php

$cliente = ClientePeer::getClienteLogado(true);

$gerenciador = new GerenciadorPontos($con = Propel::getConnection(), $logger);

$totalPontosDisponiveis = $gerenciador->getTotalPontosDisponiveisParaResgate($cliente, null, null, Extrato::TIPO_INDICACAO_DIRETA, true);

$pontosDisponiveis = $totalPontosDisponiveis < 0 ? 0 : $totalPontosDisponiveis;

$bloqueiaTransferencia = !ClientePeer::getClienteAtivoMensal($cliente->getId());

$erros = array();

if ($request->request->has('transferencia')):
    $arrTransferencia = $request->request->get('transferencia');

    $transferencia = new Transferencia();
    $transferencia->setByArray($arrTransferencia);
    $transferencia->setClienteRemetenteId($cliente->getId());

    if ($transferencia->getQuantidadePontos() > $pontosDisponiveis):
        $bonusDisponível = format_money($pontosDisponiveis);
        $erros[] = "O limite de bônus disponível é <strong>R$ $bonusDisponível</strong>.";
    endif;

    if ($transferencia->getQuantidadePontos() <= 0):
        $erros[] = 'O minimo de bônus é <strong>R$ 0,00</strong>.';
    endif;
    
    if ($transferencia->myValidate($erros) && !$erros):

        $transferencia->save($con);
        $gerenciador->transferirPontos(
            $cliente,
            $transferencia->getClienteRelatedByClienteDestinatarioId(),
            $transferencia->getQuantidadePontos(),
            $transferencia->getId()
        );

        FlashMsg::success('Transferência de bônus realizada com sucesso.');
        
        redirect('/minha-conta/transferencia');
    endif;
endif;

foreach ($erros as $erro):
    FlashMsg::danger($erro);
endforeach;
