<?php
/* @var $container \QPress\Container\Container */

if ($container->getRequest()->getMethod() == 'POST') :
    if ($container->getRequest()->request->get('CEP')) :
        $container->getSession()->set('CEP_SIMULACAO', $container->getRequest()->request->get('CEP'));
    endif;
endif;

if ($container->getRequest()->query->get('cancelar-simulacao-frete')) :
    $container->getSession()->remove('CEP_SIMULACAO');
endif;
