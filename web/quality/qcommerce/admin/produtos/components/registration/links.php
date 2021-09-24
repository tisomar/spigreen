<?php
if ($object->isNew() == false && !$getProdutoTaxa) {
    ?><a href="<?php echo get_url_admin() ?>/produtos/duplicar/<?php echo $object->getId() ?>" class="popup-iframe btn btn-green"><i class="icon-external-link"></i> Duplicar produto</a><?php
}
?>

<?php

$back = new \PFBC\Element\BackButton($config['routes']['list']);
$back->render();

