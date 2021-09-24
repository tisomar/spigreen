<?php
if ($container->getRequest()->request->get('id')) {
    BannerPeer::click($container->getRequest()->request->get('id'));
}
