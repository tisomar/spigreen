<?php
if (!isset($object)) {
    $object = null;
}

if (isAjax()) {
    header('Content-Type: text/html; charset=UTF-8');

    if (!isset($_GET['pagina'])) {
        exit('.');
    }

    include QCOMMERCE_DIR . '/admin/includes/config.inc.php';

    $pagina = $_GET['pagina'];

    $registros = SeoPeer::getRegistrosByPagina($pagina);
} else {
    $registros = SeoPeer::getRegistrosByPagina($object->getPagina());
}

if (!is_null($registros)) {
    ?>
    <div class="form-group">
        <label class="col-sm-3 control-label" for="registros">
            Registros:
        </label>
        <div class="col-sm-6">
            <?php
            echo get_form_select($registros, $object instanceof Seo ? $object->getRegistroId() : null, array(
                'id' => 'registros',
                'class' => 'select2',
                'name' => 'data[REGISTRO_ID]',
                'style' => 'width: 100%'
            ));
            ?>
        </div>
    </div>
    <hr style="margin-top: 0;">

    <script>
        $(function() {
            $(".select2").select2({width: 'resolve'});
        });
    </script>
    <?php
}


