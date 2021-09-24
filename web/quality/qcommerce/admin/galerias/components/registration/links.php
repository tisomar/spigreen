<?php

use PFBC\Element;

if ($object->isNew()) {
    /*?>
    <a class="btn btn-green" disabled href=":;">
        <span class="icon-camera"></span> Gerenciar Imagens
    </a>
    <?php*/
} else {
    /*$urlImageModule = get_url_admin() . '/galerias-arquivos/list/?context=' . GaleriaPeer::OM_CLASS . '&reference=' . $object->getId();
    ?>
    <a class="btn btn-green" href="<?php echo $urlImageModule; ?>" >
        <span class="icon-camera"></span> <span class="hidden-xs">Gerenciar Imagens</span>
    </a>
    <?php*/
}

$back = new Element\BackButton($config['routes']['list']);
$back->render();
