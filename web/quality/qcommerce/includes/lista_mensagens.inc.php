<?php
if (isset($_SESSION['']) && !empty($_SESSION['FLASH_MESSAGE'])) :
    foreach ($_SESSION['FLASH_MESSAGE'] as $tipo => $mensagens) {
        echo $tipo;
    }
endif;
