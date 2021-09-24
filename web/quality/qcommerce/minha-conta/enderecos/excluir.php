<?php
$id = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : null;

try {
    $nbRecords = EnderecoQuery::create()
            ->filterById($id)
            ->filterByClienteId(ClientePeer::getClienteLogado()->getId())
            ->delete();

    if ($nbRecords > 0) {
        FlashMsg::success('Endereço excluído com sucesso!');
    } else {
        FlashMsg::danger('Este registro não está mais disponível!');
    }
} catch (Exception $e) {
    FlashMsg::danger('Não foi possível excluir este endereço!');
}

redirect('/minha-conta/enderecos/');
