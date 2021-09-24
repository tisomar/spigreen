<style>
    .box-form-leads {
        background-color: #A1E63A;
        color: #fff;
        margin-bottom: -40px;
    }
    .padding-form-leads {
        padding: 15px 20px;
    }
    @media (min-width: 768px){
        .form-horizontal .control-label {
            text-align: right;
            margin-bottom: 0;
            padding-top: 7px;
        }
    }
    label {
        display: inline-block;
        max-width: 100%;
        margin-bottom: 5px;
    }
    @media (min-width: 768px){
        .input-lead {
            min-height: 70px;
        }
    }

    .jumbotrom#leads-page {
        background-color: #dfdfdf;
    }


</style>
<?php
$strIncludesKey = 'contato';
include("actions/leads.actions.php");
//include("../includes/header.inc.php");

/* @var $container \QPress\Container\Container */

use QPress\Template\Widget;

include_once QCOMMERCE_DIR . '/includes/head.php';

//$objFale = ConteudoPeer::retrieveByPKWithI18n(Conteudo::CONTATO_FALE_COM_A_GENTE);
?>
<body>
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.cart.tracking.php'; ?>
<?php Widget::render('general/header');?>
<?php //include_once __DIR__ . '/../includes/noscript.inc.php'; ?>
<?php //include_once __DIR__ . '/../includes/topo.inc.php'; ?>
<?php //echo get_contents(__DIR__ . '/../includes/breadcrumb.inc.php',
//    array('links' => array('Home' => '/home', _trans('breadcrumb.leads') => ''))); ?>

<div class="jumbotrom" id="leads-page">
    <div id="contact-page" class="container">

        <?php FlashMsg::display(); ?>


        <div class="row">

            <div class="col-xs-12 col-sm-8">
                <h1 class="title"><?php //echo escape(_trans('title.leads')) ?></h1>
                <?php echo _mostrarConteudoDescricao('16'); ?>

            </div>
            <div class="col-xs-12 col-sm-4 box-form-leads">
                <?php include_once __DIR__ . '/components/cadastro.form.php';?>

            </div>



        </div>

    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xs-12 title">
            <?php echo _mostrarConteudoDescricao('17');?>
        </div>
    </div>
</div>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<script src="<?php echo ROOT_PATH ; ?>/admin/assets/plugins/form-inputmask/jquery.inputmask.bundle.min.js" ></script>
<script>
    $("#form-cadastro-clientes input").each(function () {
        if($(this).data("inputmask")){
            $(this).inputmask();
        }
    });
    <?php if (isset($_SESSION['modal-leads']) && $_SESSION['modal-leads'] == 'sim') : ?>
    $('#myModal').modal('show');
    <?php endif;
    unset($_SESSION['modal-leads']); ?>


</script>
</body>
</html>
