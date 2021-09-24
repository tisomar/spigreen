<?php

    $obj = DistribuidorEventoQuery::create()->findPk($_GET['id']);

    $obj->setStatus(DistribuidorEvento::STATUS_FINALIZADO);
    $obj->setDistribuidorTemplateIdPerda($_GET['motivo']);

    $obj->save();
    
    echo $obj->getClienteDistribuidor()->getId();
