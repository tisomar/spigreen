<?php
use \QPress\Template\Widget;
?>
    <footer class="footer" role="contentinfo">
        <?php if (!$isLightbox) : ?>
            <div class="hidden-md hidden-lg">
                <div class="container">
                    <?php Widget::render('forms/newsletter') ?>
                </div>
            </div>

            <nav class="site-map">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 col-md-2 scrollme">
                            <h4 class="tit">Comprando</h4>
                            <ul class="list-unstyled">
                                <?php if (Config::get('mostrar_marcas')) : ?>
                                    <li><a href="<?php echo get_url_site(); ?>/marcas">Marcas</a></li>
                                <?php endif; ?>
                                <li><a href="<?php echo get_url_site(); ?>/documentos/seguranca" data-lightbox="iframe" title="Segurança">Segurança</a></li>
                                <li><a href="<?php echo get_url_site(); ?>/documentos/termos" data-lightbox="iframe" title="Termos de Uso">Termos de uso</a></li>
                                <li><a href="<?php echo get_url_site(); ?>/documentos/troca" data-lightbox="iframe" title="Devolução e Troca">Política de troca e devolução</a></li>
                                <li><a href="<?php echo get_url_site(); ?>/documentos/politica" data-lightbox="iframe" title="Política de Privacidade">Política de privacidade</a></li>
                            </ul>
                        </div>

                        <div class="col-xs-12 col-sm-4 col-md-2 scrollme">
                            <h4 class="tit">Institucional</h4>
                            <ul class="list-unstyled">
                                <li><a href="<?php echo get_url_site(); ?>/empresa">Sobre nós</a></li>
                                <li><a href="<?php echo get_url_site(); ?>/contato">Contato</a></li>
                            </ul>
                        </div>

                        <div class="col-xs-12 col-sm-4 col-md-2">
                            <h4 class="tit">Atendimento</h4>
                            <ul class="list-unstyled">
                                <?php if (Config::get('has_brtalk')) : ?>
                                    <li><a href="<?php echo get_url_site(); ?>/brtalk/cliente/index.php" target="_blank" title="Seja atendido através de nosso chat online">Atendimento online</a></li>
                                <?php endif; ?>
                                <li><a href="<?php echo get_url_site(); ?>/minha-conta/dados" title="Minha Conta">Minha Conta</a></li>
                                <li><a href="<?php echo get_url_site(); ?>/minha-conta/pedidos" title="Meus Pedidos">Meus Pedidos</a></li>
                                <li><a href="<?php echo get_url_site(); ?>/perguntas-frequentes" title="Tire suas dúvidas acessando a seção de perguntas frequentes">Perguntas Frequentes</a></li>
                                <li><a href="<?php echo get_url_site(); ?>/contato" title="Entre em contato através de nosso telefone."><?php Widget::render('general/phone') ?></a></li>
                            </ul>
                        </div>

                        <div class="col-md-5 col-md-offset-1 visible-md visible-lg">
                            <div class="aside">
                                <?php Widget::render('forms/newsletter') ?>
                                <div class="row">
                                    <div class="col-xs-10">
                                        <?php Widget::render('general/social') ?>
                                    </div>
                                    <div class="col-xs-2">
                                        <div class="pull-right">
                                            <?php echo Config::get('ebit_selo_rodape') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="hidden-md hidden-lg">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12">
                            <?php Widget::render('general/social') ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="seals-icons">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-7 payment-icons" >
                            <?php Widget::render('general/payment-icons') ?>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-5 security-seals-icons" >
                            <?php Widget::render('general/security-seals-icons') ?>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-2">
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
                            <?php Widget::render('general/endereco') ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </footer>

    </div>

    <div>
        <a class="whats-desk" href="https://api.whatsapp.com/send?phone=556530412000" target="_blank">
            <!--            <img class="whatsapp" src="--><?//= asset('/img/whatsapp/whatsapp-desk.png') ?><!--" />-->
            <img class="whatsapp" src="<?= asset('/img/whatsapp/whatsapp-mobi.png') ?>" />
        </a>
        <a class="whats-mobi" href="https://api.whatsapp.com/send?phone=556530412000" target="_blank">
            <img class="whatsapp" src="<?= asset('/img/whatsapp/whatsapp-mobi.png') ?>" />
        </a>
    </div>

    <script type="text/javascript" src="<?php echo asset('/js/libs/owl2/owl.carousel.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('/admin/assets/plugins/form-maskmoney/jquery.maskMoney.js') ?>"></script>
    <script type="text/javascript" src="<?php echo asset('/js/min/footer.js') ?>"></script>

    <script src="<?= asset('/js/libs/select2/select2.full.min.js') ?>"></script>
    <script src="<?= asset('/js/libs/select2/pt-BR.js') ?>"></script>
    <link rel="stylesheet" href="<?= asset('/js/libs/select2/select2.min.css') ?>">

    <style>
        .whatsapp {
            position: fixed;
            bottom: 3%;
            right: 2%;
            padding: 10px;
            /*margin-bottom: 100px;*/
            z-index: 10000000;
            width: 80px;
        }
        .whats-desk {
            display: block;
        }
        .whats-mobi {
            display: none;
        }
        .whats-mobi img {
            /*width: 100px;*/
            width: 60px;
        }
        @media (max-width: 767px) {
            .whats-desk {
                display: none;
            }
            .whats-mobi {
                display: block;
            }
        }
    </style>

<?php
if (!isLocalhost()) {
    echo Config::get('google_analytics');
}
include QCOMMERCE_DIR . '/includes/livereload.php';
Widget::render('popup/initial-popup');
Widget::render('general/initial-modal');
if (Config::get('has_zopim_chat')) {
    echo Config::get('zopim_chat_script');
}
