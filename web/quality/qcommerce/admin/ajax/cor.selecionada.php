<?php
 
if (isset($_GET['cor']) && is_numeric($_GET['cor'])) {
    $objCor = BibliotecaCorPeer::retrieveByPK($_GET['cor']);
    
    if ($objCor instanceof BibliotecaCor) { ?>
      <label>Cor Selecionada:</label>
      <div style="float: left; margin: 0 0 10px 20px;">
        <?php if (!is_null($objCor->getImagem())) { ?>
              <div style="width: 25px; height: 25px;"><?php echo $objCor->getThumb('width=25&height=25&cropratio=1'); ?></div>
        <?php } else { ?>
              <div style="width: 25px; height: 25px; background:#<?php echo $objCor->getRgb(); ?>"></div>
        <?php } ?>
      </div>
      <br class="clear" />
    <?php }
}
?>
