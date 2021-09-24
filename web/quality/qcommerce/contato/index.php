<?php
use QPress\Template\Widget;
$strIncludesKey = 'contato';

include __DIR__ . "/actions/contato.actions.php";

include QCOMMERCE_DIR . "/includes/head.php";
?>
<body itemscope itemtype="http://schema.org/ContactPage" data-page="contato">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.lead.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<main role="main">
    <?php
    Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Contato' => '')));
    Widget::render('general/page-header', array('title' => 'Contato'));
    Widget::render('components/flash-messages');
    ?>

    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <div class="info-contact">
                    <div class="text-center">
                        <h2>Fale conosco</h2>
                        <?php echo ConteudoPeer::get('contato_fale_conosco')->getDescricao() ?>
                        <!-- <span><iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1921.5919487695326!2d-56.077580294312774!3d-15.581820462801856!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTXCsDM0JzU0LjYiUyA1NsKwMDQnMzYuOSJX!5e0!3m2!1spt-BR!2sbr!4v1571056648760!5m2!1spt-BR!2sbr" width="500" height="300" frameborder="0" style="border:0;" allowfullscreen=""></iframe></span> -->
                        <ul class="list-unstyled">
                            <!-- <li>
                                <span class="<?php icon('phone'); ?>"></span>
                                <?php echo Config::get('empresa_telefone_contato'); ?>
                            </li>
                            <li>
                                <span class="<?php icon('envelope-o'); ?>"></span>
                                <?php echo Config::get('email_contato'); ?>
                            </li> -->
                        </ul>
                        
                        <br>
                        <h2>Atendimento</h2>
                        <?php echo ConteudoPeer::get('contato_atendimento_online')->getDescricao() ?>
                        <br>
                        <h2>Tire suas dúvidas</h2>
                        <p>
                            Ache suas respostas na nossa central de dúvidas.
                            <br>
                            <b><a href="<?php echo $root_path; ?>/perguntas-frequentes">Clique aqui para acessar</a></b>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6">
                <?php \QPress\Template\Widget::render('forms/contato'); ?>
            </div>
        </div>
    </div>
    <!-- <div id="google-maps"></div> -->
</main>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA5mK4RjxwhRa4ZyjMbdrYjSZraJwEP-M4&amp;sensor=false"></script>
<script>

    function initialize (){

        var styles = [
            {
                featureType: 'landscape',
                elementType: 'all',
                stylers: [
                    { hue: '#e0e2e7' },
                    { saturation: -53 },
                    { lightness: 2 },
                    { visibility: 'on' }
                ]
            },{
                featureType: 'road',
                elementType: 'labels',
                stylers: [
                    { hue: '#cacbd0' },
                    { saturation: -94 },
                    { lightness: 46 },
                    { visibility: 'on' }
                ]
            },{
                featureType: 'poi',
                elementType: 'geometry',
                stylers: [
                    { hue: '#dcdfe4' },
                    { saturation: -70 },
                    { lightness: 45 },
                    { visibility: 'simplified' }
                ]
            },{
                featureType: 'road.arterial',
                elementType: 'geometry',
                stylers: [
                    { hue: '#babfc5' },
                    { saturation: -91 },
                    { lightness: -2 },
                    { visibility: 'simplified' }
                ]
            },{
                featureType: 'road.local',
                elementType: 'geometry',
                stylers: [
                    { hue: '#FFFFFF' },
                    { saturation: -100 },
                    { lightness: 100 },
                    { visibility: 'simplified' }
                ]
            },{
                featureType: 'road.highway',
                elementType: 'geometry',
                stylers: [
                    { hue: '#babfc5' },
                    { saturation: -91 },
                    { lightness: 31 },
                    { visibility: 'simplified' }
                ]
            },{
                featureType: 'water',
                elementType: 'all',
                stylers: [
                    { hue: '#d9dce1' },
                    { saturation: -74 },
                    { lightness: 44 },
                    { visibility: 'on' }
                ]
            },{
                featureType: 'transit',
                elementType: 'all',
                stylers: [
                    { hue: '#222222' },
                    { saturation: 0 },
                    { lightness: -82 },
                    { visibility: 'off' }
                ]
            },{
                featureType: 'poi.park',
                elementType: 'labels',
                stylers: [
                    { hue: '#cacbd0' },
                    { saturation: -86 },
                    { lightness: 11 },
                    { visibility: 'on' }
                ]
            }
        ];

        var myLatlng = new google.maps.LatLng(<?php echo Config::get('googlemaps_latitude') ?>,<?php echo Config::get('googlemaps_longitude') ?>);

        var options = {
            mapTypeControlOptions: {
                mapTypeIds: [ 'Styled']
            },
            center: myLatlng,
            zoom: 16,
            scrollwheel: false,
            zoomControl: false,
            mapTypeId: 'Styled',
            streetViewControl:false,
            navigationControl:false,
            mapTypeControl:false
        };
        function addMarker(){
            var marker = new google.maps.Marker({
                map:map,
                position:myLatlng,
                title:'<?php echo escape($strTitle); ?>',
                icon: '<?php echo asset('/img/min/map-marker.png'); ?>'
            });
        }

        var div = document.getElementById('google-maps');
        var map = new google.maps.Map(div, options);
        var styledMapType = new google.maps.StyledMapType(styles, { name: 'Styled' });
        map.mapTypes.set('Styled', styledMapType);

        addMarker();
    }
    google.maps.event.addDomListener(window, 'load', initialize);

</script>
</body>
</html>
