<?php
use QPress\Template\Widget;
$strIncludesKey = '';
require_once __DIR__ . '/actions/identificacao.actions.php';
include_once __DIR__ . '/../includes/head.php';
?>
<body itemscope itemtype="http://schema.org/WebPage">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
    <?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
    <?php Widget::render('general/header'); ?>

    <main role="main">
        <?php
            Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Login' => '')));
            Widget::render('general/page-header', array('title' => 'Login'));
            Widget::render('components/flash-messages');
            Widget::render('forms/login', array('redirect' => isset($redirect) ? $redirect : null));
        ?>
    </main>


    <?php /* ?>

     @todo
     Módulo de login com facebook, por enquanto ele retorna os dados e direciona para o cadastro, ver como preencher o cadastro dai
    <script>
        function statusChangeCallback(response) {
            console.log('statusChangeCallback');
            console.log(response);
            if (response.status === 'connected') {
                testAPI();
            } else if (response.status === 'not_authorized') {
                document.getElementById('status').innerHTML = 'Please log ' +
                'into this app.';
            } else {
                document.getElementById('status').innerHTML = 'Please log ' +
                'into Facebook.';
            }
        }

        function checkLoginState() {
            FB.getLoginStatus(function(response) {
                statusChangeCallback(response);
            });
        }

        window.fbAsyncInit = function() {
            FB.init({
                appId      : '775517659167960', // Id da app
                cookie     : true,
                xfbml      : true,
                version    : 'v2.1'
            });

            FB.getLoginStatus(function(response) {
                statusChangeCallback(response);
            });
        };

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        function testAPI() {
            console.log('Welcome!  Fetching your information.... ');
            FB.api('/me', function(response) {
                console.log(JSON.stringify(response));
                document.getElementById('status').innerHTML = 'Thanks for logging in, ' + response.name + '!';
                <?php echo redirect('/cadastro'); ?>
            });
        }
    </script>

    <fb:login-button scope="public_profile,email" onlogin="checkLoginState();"></fb:login-button>

    <div id="status"></div>

    <?php */ ?>
    <?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
</body>
</html>

