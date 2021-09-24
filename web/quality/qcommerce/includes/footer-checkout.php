<?php use \QPress\Template\Widget; ?>
    <div class="push"></div>
</div><!-- wrapper -->
    <footer class="footer footer-checkout" role="contentinfo">
        <div class="contact">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        Dúvidas? Entre em contato:
                        <span class="contact-phone"><?php echo Config::get('empresa_telefone_contato') ?></span>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-4">
                        <div class="text-center">
                            <a href="<?php echo get_url_site(); ?>/documentos/seguranca" data-lightbox="iframe" title="Segurança">Segurança</a>
                            <b>&bull;</b> <a href="<?php echo get_url_site(); ?>/documentos/termos" data-lightbox="iframe" title="Termos de Uso">Termos de uso</a></span>
                            <br>
                            <a href="<?php echo get_url_site(); ?>/documentos/troca" data-lightbox="iframe" title="Devolução e Troca">Devolução e trocas</a></span>
                            <b>&bull;</b> <a href="<?php echo get_url_site(); ?>/documentos/politica" data-lightbox="iframe" title="Política de Privacidade">Política de privacidade</a></span>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-2 col-md-4">
                        <span class="icon-ambiente-seguro"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="seals-icons">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-8 payment-icons" >
                        <?php Widget::render('general/payment-icons') ?>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-4 security-seals-icons" >
                        <?php Widget::render('general/security-seals-icons') ?>
                    </div>

                    <div class="col-xs-12 col-sm-12">
                        <div class="col-xs-12 col-md-6 redes-sociais" style="margin-top: 10px; width: 100%">
                            <a href="https://www.facebook.com/spigreennatural/" title="Facebook" target="_blank" style="display: inline-block; margin-right: 10px">
                                <img style="max-width: 28px; max-height: 28px;" class="img-responsive" alt="Facebook" title="Facebook"  src="<?php echo get_url_site() ?>/img/icons/faceicon.jpg" />
                            </a>
                            <a href="https://www.instagram.com/spigreen/" title="Instagram" target="_blank" style="display: inline-block; margin-left: 10px" >
                                <img style="max-width: 28px; max-height: 28px;" class="img-responsive" alt="Instragram" title="Instagram" src="<?php echo get_url_site() ?>/img/icons/instaicon.jpg" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="basefooter">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <?php Widget::render('general/endereco'); ?>
                    </div>
                </div>
            </div>
        </div>

    </footer>


<script type="text/javascript" src="<?php echo asset('/js/min/footer.js') ?>"></script>

<?php if (is_file($request->server->get('DOCUMENT_ROOT') . $request->getBasePath() . '/js/min/' . $strIncludesKey . '.js')) : ?>
    <script type="text/javascript" src="<?php echo asset('/js/min/' . $strIncludesKey . '.js') ?>"></script>
<?php endif; ?>

<?php echo Config::get('google_analytics'); ?>

<?php echo Config::get('javascript_body_final'); ?>

<?php Widget::render('general/initial-modal'); ?>

<?php
if (Config::get('has_zopim_chat')) {
    echo Config::get('zopim_chat_script');
}
?>
<?php include QCOMMERCE_DIR . '/includes/livereload.php' ?>