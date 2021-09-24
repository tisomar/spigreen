<?php
$view = isset($_POST['view']) && $_POST['view'] == 1 ? true : false;
$alertaFechada = isset($_SESSION["adiar_alertas"]) && $_SESSION["adiar_alertas"] == true ? true : false;
$idDocumento = isset($_POST['idDocumento']) ? $_POST['idDocumento'] : null;
$cliente = ClientePeer::getClienteLogado();
$locale = QPTranslator::getLocale();
$alertaDocumento = $cliente->mensagensPendentes();
if (!$alertaFechada && $view == false && $alertaDocumento != null) {
    if ($alertaDocumento['alerta']['TIPO_MENSAGEM'] != _trans('alertas.aniversariantes', array(), null, $locale)) {
        $alertaPdfs = $alertaDocumento['pdfs'];
        $pdfsArray = array();
        foreach ($alertaPdfs as $pdf) {
            /**@var DocumentoAlertaPdf $pdf */
            $pdfObj = array();
            $pdfObj['strPathAlertaPdf'] = $pdf->strPathAlertaPdf;
            $pdfObj['nomeArquivo'] = $pdf->getNomeArquivo();
            $pdfObj['nomeOriginal'] = $pdf->getNomeOriginal();
            $pdfsArray[] = $pdfObj;
        }
    }
    echo json_encode(array('alerta' => $alertaDocumento['alerta'], 'pdfs' => isset($pdfsArray) ? $pdfsArray : null, 'pendentes' => $alertaDocumento['quantidadeMensagens']), JSON_UNESCAPED_UNICODE);
} elseif ($view == true) {
    $alertaDocumento = $cliente->getMessagemById($idDocumento);

    $alertaPdfs = $alertaDocumento['pdfs'];
    $pdfsArray = array();
    foreach ($alertaPdfs as $pdf) {
        /**@var DocumentoAlertaPdf $pdf */
        $pdfObj = array();
        $pdfObj['strPathAlertaPdf'] = $pdf->strPathAlertaPdf;
        $pdfObj['nomeArquivo'] = $pdf->getNomeArquivo();
        $pdfObj['nomeOriginal'] = $pdf->getNomeOriginal();
        $pdfsArray[] = $pdfObj;
    }
    echo json_encode(array('alerta' => $alertaDocumento['alerta'], 'pdfs' => $pdfsArray), JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(array('alerta' => null, 'pdfs' => null, 'pendentes' => $cliente->quantidadeMensagensPendentes()), JSON_UNESCAPED_UNICODE);
}
