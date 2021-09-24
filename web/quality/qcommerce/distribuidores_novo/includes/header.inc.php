<?php

    require __DIR__ . '/../includes/config.inc.php';

include_once __DIR__ . '/../../includes/seo.inc.php';

    
    $iconLigar = "<span class=\"visible-xs \"> <i class=\"fa fa-phone\"> </i></span>";
    $iconReuniao = "<span class=\"visible-xs \"> <i class=\"fa fa-users\"> </i></span>";

?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Agenda do Distribuidor | Spigreen</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="keywords" content="<?php echo $strKeyWord; ?>">
        <meta name="description" content="<?php echo $strDescription; ?>">
        <meta name="author" content="www.qualitypress.com.br">

        <link rel="icon" href="<?php echo $root_path ?>/distribuidor_scripts/assets/img/favicon.png">

        <script src="<?php echo $root_path ?>/distribuidor_scripts/assets/js/min/header.js"></script>

        <link rel="stylesheet" href="<?php echo $root_path?>/backend/css/neon/font-icons/entypo/css/entypo.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0-rc2/css/bootstrap-glyphicons.css">
        <link rel="stylesheet" type="text/css" href="<?php echo $root_path ?>/distribuidor_scripts/assets/js/libs/AnimatedBorderMenus/css/demo.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $root_path ?>/distribuidor_scripts/assets/js/libs/AnimatedBorderMenus/css/icons.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $root_path ?>/distribuidor_scripts/assets/js/libs/AnimatedBorderMenus/css/style1.css" />
        <link rel="stylesheet" href="<?php echo $root_path ?>/distribuidor_scripts/assets/css/libs.css">
        <link rel="stylesheet" href="<?php echo $root_path ?>/distribuidor_scripts/assets/css/custom.css">
        <link rel="stylesheet" href="<?php echo $root_path ?>/distribuidor_scripts/assets/css/sweetalert.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <script src="<?php echo $root_path ?>/distribuidor_scripts/assets/js/libs/AnimatedBorderMenus/js/modernizr.custom.js"></script>
        <script src="<?php echo $root_path ?>/distribuidor_scripts/assets/js/sweetalert.js"></script>
        <!--[if lt IE 9]><script src="<?php echo $root_path ?>/distribuidor_scripts/assets/js/libs/ie8-responsive-file-warning.js"></script><![endif]-->

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <style>
            .zopim{
                right: 85px !important;
            }
        </style>
        
        
        <script type="text/javascript">
            var root_path = <?php echo json_encode($root_path) ?>;
            //var distribuidores_root_path = <?php //echo json_encode($distribuidores_root_path_novo) ?>//;
        </script>
    </head>
