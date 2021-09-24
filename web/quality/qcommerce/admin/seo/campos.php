<?php
if (isAjax()) {
    header('Content-Type: text/html; charset=UTF-8');

    if (!isset($_GET['pagina'])) {
        exit('.');
    }

    include QCOMMERCE_DIR . '/admin/includes/config.inc.php';

    $pagina = $_GET['pagina'];
    $registro_id = $_GET['registro_id'] == '' ? null : $_GET['registro_id'];

    $object = SeoQuery::create()
            ->filterByRegistroId($registro_id)
            ->filterByPagina($pagina)
            ->findOne();


    if (!$object instanceof Seo) {
        $object = new Seo();
    }
}

# -------------------------------------------------

/**
 * Campo TITLE
 */
$field = new PFBC\Element\Textbox('Title', 'data[META_TITLE]', array(
    "value" => $object->getMetaTitle(),
        ));
?>
<div class="form-group">
    <label class="col-sm-3 control-label" for="">
        Title:
    </label>
    <div class="col-sm-6">
<?php $field->render(); ?>
    </div>
</div>
<?php
# -------------------------------------------------

/**
 * Campo Keywords
 */
$field = new PFBC\Element\Textbox('Keywords', 'data[META_KEYWORDS]', array(
    "value" => $object->getMetaKeywords(),
    "class" => "input-token",
    "placeholder" => "Adicionar..."
        ));
?>
<div class="form-group">
    <label class="col-sm-3 control-label" for="">
        Keywords:
    </label>
    <div class="col-sm-6">
<?php $field->render(); ?>
    </div>
</div>
<?php
# -------------------------------------------------

/**
 * Campo Description
 */
$field = new PFBC\Element\Textarea('Description', 'data[META_DESCRIPTION]', array(
    "value" => $object->getMetaDescription(),
        ));
?>
<div class="form-group">
    <label class="col-sm-3 control-label" for="">
        Description:
    </label>
    <div class="col-sm-6">
<?php $field->render(); ?>
    </div>
</div>

<?php
if (isAjax()) {
    ?>
    <script>
        $(function() {
            $('.input-token').tokenfield();
        });
    </script>
    <?php
}


