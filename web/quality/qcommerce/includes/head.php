<?php
include_once QCOMMERCE_DIR . '/includes/seo.inc.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta name="theme-color" content="<?php echo Config::get('mail_rgb') ?>">
    <meta charset="UTF-8">
    <meta name="author" content="www.qualitypress.com.br">
    <meta name="keywords" content="<?php echo escape($strKeyWord); ?>">
    <meta name="description" content="<?php echo escape($strDescription); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0">
    <title><?php echo escape($strTitle); ?></title>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo Config::getFavicon()->forceUrlImageResize('width=32&height=32&cropratio=1:1'); ?>">

    <?php if (!isset($meta['noindex']) || $meta['noindex'] == true) : ?>
        <meta name="robots" content="index,follow">
    <?php else : ?>
        <meta name="robots" content="noindex,nofollow">
    <?php endif; ?>

    <?php foreach (\ClearSaleMapper\Manager::get() as $csKey => $csValue) : ?>
        <meta name="cs:<?php echo $csKey ?>" content="<?php echo $csValue ?>">
    <?php endforeach; ?>

    <script type="text/javascript" src="<?php echo asset('/js/min/header.js'); ?>"></script>
    <script type="text/javascript">
        window.root_path = '<?php echo $request->getBaseUrl() ?>';
        window.request = new Object();
        window.request.basePath = '<?php echo $request->getBasePath() ?>';
        window.request.baseUrl = '<?php echo $request->getBaseUrl() ?>';
    </script>

    <link rel="stylesheet" type="text/css" href="<?php echo asset('/css/custom.css'); ?>">

    <?php echo Config::get('comodo_head'); ?>
    <?php echo Config::get('javascript_head'); ?>

    <?php if (isLocalhost()) : ?>
        <style>
            body::before {
                content: "xs";
                position: fixed;
                bottom: 0;
                left: 0;
                z-index: 9999999;
                background-color: #000;
                color: #fff;
                padding: 5px 10px;
                opacity: 0.5;
            }
            @media (min-width : 768px) { body::before { content: "sm"; }}
            @media (min-width : 992px) { body::before { content: "md"; }}
            @media (min-width : 1200px) { body::before { content: "lg"; }}
        </style>
    <?php endif; ?>

    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window,document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '803385360171919');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" src="https://www.facebook.com/tr?id=803385360171919&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->

    <!-- Global site tag (gtag.js) - Google Ads: 718444471 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-718444471"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'AW-718444471');
    </script>

    <?php
    // Carrega script apenas na página de confirmação do pedido
    if (strpos(get_url_caminho(), '/checkout/confirmacao/') !== false) :
    ?>
        <!-- Event snippet for Site Store Spigreen conversion page -->
        <script>
            gtag('event', 'conversion', {'send_to': 'AW-718444471/uHrzCMKasswBELevytYC'});
        </script>
    <?php
    endif;
    ?>
</head>

<?php
if (ClientePeer::getClienteLogado(true) != null) :
    include_once __DIR__ . '/../minha-conta/alertas/alerta-modal.php';
endif;
?>