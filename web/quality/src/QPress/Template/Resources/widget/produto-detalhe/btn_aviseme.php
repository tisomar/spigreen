<?php
if (Config::get('has_aviseme')) {
    ?>
        <a href="<?php echo get_url_site() . '/produtos/avise-me/?pvid=' . $variacaoId ?>" class="avise-me btn btn-primary btn-full cboxElement" title="Avise-me!" data-lightbox="iframe">
            Avise-me quando disponível
        </a>
        <script id="__initLightBox_aviseme">$(function() {
                initLightbox();
                $('#__initLightBox_aviseme').remove();
            })
        </script>
    <?php
} ?>
