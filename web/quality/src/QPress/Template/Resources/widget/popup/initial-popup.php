<?php /* if (Config::get('popup.show') && !$container->getSession()->get('viewed')): ?>
    <?php $container->getSession()->set('viewed', true); ?>
    <div id="initialpopup"></div>
    <script type="text/javascript">
        $(function() { initLightbox('#initialpopup', { open: true, href: window.root_path + '/documentos/popup/' }) });
    </script>
<?php endif; */ ?>
<?php if ($container->getSession()->get('accepted-demo-popup', true)): ?>
    
    <?php if ((Config::get('popup.show') && !$container->getSession()->get('viewed_lead', false)) || $container->getRequest()->query->has('debug-popup')): ?>
        <?php $container->getSession()->set('viewed_lead', true); ?>
        <script>
            (function($) {
                $(window).load(function () {
                    $.magnificPopup.open({
                        items: {
                            src: '<?php echo get_url_site() ?>/mailforweb/lead'
                        },
                        type: 'iframe',
                        mainClass: 'initial-popup'
                    }, 0);

                });
            })(jQuery);
        </script>
    <?php elseif ((Config::get('precadastro.popup.show') && !$container->getSession()->get('viewed_lead', false)) || $container->getRequest()->query->has('debug-popup-precadastro')): ?>
        <?php $container->getSession()->set('viewed_lead', true); ?>
        <script>
            (function($) {
                $(window).load(function () {
                    $.magnificPopup.open({
                        items: {
                            src: '<?php echo get_url_site() ?>/mailforweb/precadastro'
                        },
                        type: 'iframe',
                        mainClass: 'initial-popup'
                    }, 0);

                });
            })(jQuery);
        </script>
    <?php endif; ?>
<?php endif; ?>
