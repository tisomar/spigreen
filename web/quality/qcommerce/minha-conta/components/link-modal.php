<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 28/06/2018
 * Time: 17:25
 */

if (isset($_SESSION['MODAL_AVISO'])) {
    $message = $_SESSION['MODAL_AVISO'];
    ?>
    <a id="aviso" href="<?php echo get_url_site(); ?>/documentos/aviso" data-lightbox="iframe" title="Aviso"></a>
    <script type="text/javascript">
        $( document ).ready(function() {
            $('#aviso').trigger('click');
        });
    </script>
    <?php
}

?>
