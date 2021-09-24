    <span>
        <img src="<?php echo get_url_site() ?>/img/icons/siteseguro.png" alt="Site Seguro" title="Site Seguro" height="42px; margin-top: -20px"/>
    </span>
    <span>
        <img src="<?php echo get_url_site() ?>/img/icons/clearsale.jpg" alt="ClearSale" title="ClearSale" height="42px; margin-top: -20px"/>
    </span>



<?php if (Config::get('positive_habilitado') == 1): ?>
    <span>
        <img src="https://redefacilbrasil.com.br/web/images/icons/clearsale.jpg" alt="Positive SSL" title="Positive SSL" height="50"/>
    </span>
<?php endif; ?>

<?php if (Config::get('comodo_habilitado') == 1 && Config::get('comodo_body') != ''): ?>
    <span title="Comodo"><?php echo Config::get('comodo_body') ?></span>
<?php endif; ?>

<?php if (Config::get('ebit_selo_rodape') != ''): ?>
    <span class="icon-ebit" title="Ebit"></span>
<?php endif; ?>