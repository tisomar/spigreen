<?php
$cliente = ClientePeer::getClienteLogado();
$idAlerta = $_POST['idAlerta'];
$idAlertaDocumento = $_POST['idDocumento'];
$now = new DateTime('now');
$documentoAlerta = DocumentoAlertaQuery::create()->findOneById($idAlerta);
if ($documentoAlerta->getTipoMensagem() == 'aniversariantes' && $idAlertaDocumento == null) {
    $documentoAlertaClientes = new DocumentoAlertaClientes();
    $documentoAlertaClientes->setDocumentoAlertaId($idAlerta);
    $documentoAlertaClientes->setCliente($cliente);
    $documentoAlertaClientes->setDataLido($now);
    $documentoAlertaClientes->save();
} else {
    /** @var DocumentoAlertaClientes $documentoAlertaClientes */
    $documentoAlertaClientes = DocumentoAlertaClientesQuery::create()
        ->filterByDocumentoAlertaId($idAlerta)
        ->filterByCliente($cliente)
        ->findOne();

    $documentoAlertaClientes->setDataLido($now)->save();
}

echo json_encode(array(
    'pendentes' => $cliente->quantidadeMensagensPendentes(),
    'dataAceptacao' => $now->format('Y-m-d H:m:s'),
    'idDocumentoAlertaClientes' => $documentoAlertaClientes->getId()
));
