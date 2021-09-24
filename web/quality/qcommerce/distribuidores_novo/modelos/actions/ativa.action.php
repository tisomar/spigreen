<?php

    $objTemplateSms = DistribuidorTemplateQuery::create()->findPk($_GET['id']);

    $objTemplateSms->setAtivo(($objTemplateSms->getAtivo() ? 0 : 1));

    $objTemplateSms->save();
