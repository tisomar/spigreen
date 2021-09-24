<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 29/01/2019
 * Time: 16:00
 */

$sistema = $_POST['sistema'];
$videoId = $_POST['videoId'];

$clienteId = ClientePeer::isAuthenticad() ? ClientePeer::getClienteLogado(true)->getId() : null;

if (!is_null($clienteId)) {
    $videosVisto = ClienteAjudaPaginaViewQuery::create()
        ->filterByClienteId($clienteId)
        ->filterByVideoId($videoId)
        ->findOne();

    if ($videosVisto instanceof ClienteAjudaPaginaView && $videosVisto->getAjudaPaginaVideo()->getSistema() == $sistema) {
        echo json_encode(array('visto' => true));
        die;
    }
    $videoVisto = new ClienteAjudaPaginaView();
    $videoVisto->setClienteId($clienteId);
    $videoVisto->setVideoId($videoId);
    $videoVisto->setDataVisto(date('Y-m-d H:i:s'));
    $videoVisto->save();
    echo json_encode(array('visto' => false));
}

die;
