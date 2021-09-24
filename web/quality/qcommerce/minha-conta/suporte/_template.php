<?php
use QPress\Template\Widget;

include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php';
include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php';

Widget::render('mfp-modal/header', array(
    'title' => $title
));
?>
<main role="main">
    <div class="box-flash-messages">
        <?php Widget::render('components/flash-messages'); ?>
    </div>
    <div class="container">
        <p><?php echo $content; ?></p>
    </div>
</main>