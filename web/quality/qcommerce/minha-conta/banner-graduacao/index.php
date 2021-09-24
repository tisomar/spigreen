<?php

use QPress\Template\Widget;

$strIncludesKey = 'minha-conta-banner-graduacao';
include QCOMMERCE_DIR . "/includes/security.php";
include QCOMMERCE_DIR . "/includes/head.php";
require __DIR__ . '/actions/banner-graduacao.actions.php';
?>

<body itemscope itemtype="http://schema.org/WebPage" data-page="minha-conta-meu-plano">
<?php include_once QCOMMERCE_DIR . '/includes/javascript_body_inicio.php'; ?>
<?php include_once QCOMMERCE_DIR . '/includes/facebook_tracking/fb.general.tracking.php'; ?>
<?php Widget::render('general/header'); ?>

<link href="https://fonts.googleapis.com/css2?family=Lato:wght@300&family=Montserrat&display=swap" rel="stylesheet">
<style>
    .imputGraduacao{
        border: 0;
        outline: 0;
    }

    #banner{
       margin: 0  !important;
       padding-top: -210px  !important;
       padding:0 !important;
    }

    #banner #box-banner{
        margin:0  !important;
        padding:0 !important;
    }

    #banner #contact {
        position:relative;
        bottom:25px;
        background: #FFF;
        margin-bottom: 50px;
    }

    #box-banner {
        z-index: 99;
        position: relative;
        top: 10px;
        min-height: 400px;
        max-height: 550px;
    }

    #box-banner .bannerGraduacao{
        top: -10px;
        z-index: 2;
        position: relative;
        right:0;
    }

    #box-banner .bannerGraduacao img{
        width: 100%;
    }

    #box-banner #imgGraduacao{
        z-index: 6;
    }

    #box-banner .changedNome{
        right: 0;
        z-index: 3;
        position: relative;
        height: 100px;
        bottom: 10vmin;
    }

    #box-banner .changedNome .textCidade {
        margin-top: -6px;
    }
    
    @media only screen and (max-width: 400px) {
        #box-banner .changedNome{
            bottom: 20vmin;
        }
    }

    #box-banner .changedNome > p{
        color: #FFF;
        font-weight: 600;
        text-align:center;
        font-family: 'Lato', sans-serif;
        margin:0;
    }

    #box-banner .photoPreview{
        z-index: 1;
        position: relative;
        bottom: 400px;
    }
    
    #box-banner .photoPreview img{
        width: 250px;
        display: none;
    }

    #box-banner img{
        width: 100%;
    }

    #box-banner .changedNome{
        width: 100%;
    }

    input[type="file"] {
        display: none;
    }

    .custom-file-upload {
        border-bottom: 1px solid #ccc;
        display: inline-block;
        padding: 13px 12px;
        cursor: pointer;
    }

    .labelNome{
        width: 500px;
        margin-bottom: 10px;
    }

    .btns{
        width: 30px;
        height: 30px;
        border-radius: 50%;
        line-height: 0px;
        margin: 0 !important; 
        padding: 0 !important; 
    }

    .textNome {
        font-size: 18px;
    }

    .textCidade{
        font-size: 15px;
    }

</style>
<main role="main">
    <?php
    Widget::render('components/breadcrumb', array('links' => array('Home' => '/home', 'Minha Conta' => 0, 'Banner Graduação' => '')));
    Widget::render('general/page-header', array('title' => 'Banner Graduação'));
    Widget::render('components/flash-messages');
    ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-3">
                <?php Widget::render('general/menu-account', array('strIncludesKey' => $strIncludesKey)); ?>
            </div>
        </div>
        <div class="row">
            <?php if (!empty($graduacaoSelectList)) : ?>
                <div class="col-xs-12 col-md-9 col-md-offset-3">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4>Geração do seu banner de graduação</h4>
                        </div>
                        
                        <div class="panel-body box-main">    
                    
                            <div class="inputs col-sm-4">
                                <!-- 1° versão pegando a graduacao atingina no mes anterior -->
                                <!-- <div class="form-group col-sm-12" >
                                    <label for="graduacao">Graduacao:</label>
                                    <input type="text" class="form-control imputGraduacao" placeholder="Sua Graduação" 
                                    value="</?php echo $graduacaoAtual ?>" id="graduacao" disabled>
                                </div> -->
                                <div class="form-group col-sm-12" >
                                    <form action="/minha-conta/banner-graduacao/action/banner-graduacao.actions" id="formGraduacao" method="post">
                                        <label for="graduacao">Graduacao:</label>
                                        <select class="form-control" id="selectGraduacao" name="selectGraduacao">
                                            <?php foreach($graduacaoSelectList as $key => $value) : ?>
                                                <option value="<?= $key ?>" <?= $key == $graduacaoSelected ? 'selected' : ''?>><?= $value ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </form>
                                </div>

                                <div class="form-group col-sm-12 box-button-text">
                                    <label class="labelNome">
                                        <label for="nome">Nome:</label>
                                        <input type="text" class="form-control imputGraduacao" placeholder="Seu Nome" id="nome">
                                    </label>

                                    <label class="labelNome">
                                        <label for="cidade">Cidade - Estado:</label>
                                        <input type="text" class="form-control imputGraduacao" placeholder="Exemplo São Paulo - SP" id="cidade">
                                    </label>

                                    <button type="submit" id='btnPlusNome'  data-attr="font-size" data-val="+=4" class="btn btns btn-success">
                                        <span class="<?php icon('plus') ?>"></span> 
                                    </button>

                                    <button type="submit" id='btnMinusNome' data-attr="font-size" data-val="-=4" class="btn btns btn-success">
                                        <span class="<?php icon('minus') ?>"></span>
                                    </button>

                                    <button type="submit" id='btnArrowUp' data-attr="top" data-val="-=5" class="btn btns btn-success">
                                        <span class="<?php icon('arrow-up') ?>"></span> 
                                    </button>

                                    <button type="submit" id='btnArrowDown' data-attr="top" data-val="+=5" class="btn btns btn-success">
                                        <span class="<?php icon('arrow-down') ?>"></span>
                                    </button>

                                    <button type="submit" id='btnArrowLeft' data-attr="left" data-val="-=5" class="btn btns btn-success">
                                        <span class="<?php icon('arrow-left') ?>"></span> 
                                    </button>

                                    <button type="submit" id='btnArrowRight' data-attr="left" data-val="+=5" class="btn btns btn-success">
                                        <span class="<?php icon('arrow-right') ?>"></span>
                                    </button>

                                </div>

                                <div class="form-group col-sm-12 box-button-photo">
                                    <br>
                                    <label class="custom-file-upload labelNome">
                                        <input type="file" name='photoFile' id='photoFile'/>
                                        <i class="fa fa-cloud-upload"></i> Escolha uma foto
                                    </label>

                                    <button type="submit" id='btnPhotoPlus' data-attr="width" data-val="+=5" class="btn btns btn-success">
                                        <span class="<?php icon('plus') ?>"></span> 
                                    </button>

                                    <button type="submit" id='btnPhotoMinus' data-attr="width" data-val="-=5"  class="btn btns btn-success">
                                        <span class="<?php icon('minus') ?>"></span>
                                    </button>

                                    <button type="submit" id='btnPhotoArrowUp' data-attr="top" data-val="-=5" class="btn btns btn-success">
                                        <span class="<?php icon('arrow-up') ?>"></span> 
                                    </button>

                                    <button type="submit" id='btnPhotoArrowDown' data-attr="top" data-val="+=5" class="btn btns btn-success">
                                        <span class="<?php icon('arrow-down') ?>"></span>
                                    </button>

                                    <button type="submit" id='btnPhotoArrowLeft' data-attr="left" data-val="-=5" class="btn btns btn-success">
                                        <span class="<?php icon('arrow-left') ?>"></span> 
                                    </button>

                                    <button type="submit" id='btnPhotoArrowRight' data-attr="left" data-val="+=5" class="btn btns btn-success">
                                        <span class="<?php icon('arrow-right') ?>"></span>
                                    </button>
                                </div>

                                <div class="form-group col-sm-12">
                                    <a id="btn-Convert-Html2Image" href="#"> 
                                        <button type="button" id='downloadBtn' class="btn btn-primary">Download</button>
                                    </a>
                                </div>
                            </div>

                            <div id="banner" class="col-sm-8">
                            
                                <div id="box-banner">

                                    <div class="bannerGraduacao">
                                        <img id="imgGraduacao" src="<?php echo asset('/admin/arquivos/' . $graduacaoBanner) ?>" class="pull-center" alt="...">
                                    </div>

                                    <div class="changedNome">
                                        <p class='textNome'></p>
                                        <p class='textCidade'></p>
                                    </div>

                                    <div class="photoPreview">
                                        <img src="<?= asset('/admin/assets/js/images/animated-overlay.gif')?>" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-xs-12 col-md-9 col-md-offset-3">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4>Geração do seu banner de graduação</h4>
                        </div>
                        
                        <div class="panel-body">   
                            <h4>Opss!</h4><br>
                            <p> Percebemos que no momento sua graduação não se enquadra nos requisitos da geração de banner automática. <br>

                            <p>Isso ocorre por um dos motivos abaixo:</p>
                            
                            <p>1 - No momento você não possui graduação.</p>
                            <p>2 - Sua graduação é superior a de Esmeralda, no momento seus banners estão sendo elaborados manualmente.</p> 

                            <p>Para maiores informações, favor entrar em contato com a equipe Spigreen.</p>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</main>

<script type='text/javascript' src="<?= asset('/admin/assets/js/jquery-1.10.2.min.js')?>"></script>
<script src="<?= asset('/admin/assets/plugins/dom-to-image/dom-to-image.min.js')?>"></script> 
<script>
    $(document).ready(function() { 

        const photoFile = $('#photoFile');
        const photoPreview = $('.photoPreview img');
        photoFile.on('change', function() {
            const file = this.files[0];
            
            if(file) {
                var reader = new FileReader();
                photoPreview.css('display', 'block');
                
                reader.addEventListener("load", function() {
                    photoPreview.attr('src', this.result);
                })

                reader.readAsDataURL(file);
            }
        })

        $("#nome").keyup(function(){
            $('.textNome').html($('#nome').val())
        });

        $("#cidade").keyup(function(){
            $('.textCidade').html($('#cidade').val())
        });

        $('.box-button-photo button').on('click', function() {
            let attr = $(this).data('attr');
            let value =  $(this).data('val');
            if(attr === 'width') {
                $('.photoPreview > img').css(attr, value);
            }else{
                $('.photoPreview').css(attr, value);
            }
        })

        $('.box-button-text button').on('click', function() {
            let attr = $(this).data('attr');
            let value =  $(this).data('val');
            if(attr == 'font-size') {
                console.log('teste1');
                $('.changedNome p').css(attr, value);
            }else{
                console.log('teste2');
                $('.changedNome').css(attr, value);
            }
        })

        $("#btn-Convert-Html2Image").on('click', function() { 

            var node = document.getElementById('banner');
            var options = {
                style: {
                    'zoom': 3.7,
                    'border': 0,
                    'outline': 0,
                },
                width: 2010,
                height: 2010,
            };

            domtoimage.toPng(node, options)
            .then(function (dataUrl) {

                var link = document.createElement('a');
                link.download = 'banner-graduacao.png';
                link.href = dataUrl;
                link.click();
            });
        }); 

        $('#selectGraduacao').on('change', function() {
            const graduacaoSelecionada = $(this).val();
            $('#formGraduacao').submit();
        })
    }); 
</script>

<?php include QCOMMERCE_DIR . '/includes/footer.php'; ?>
<?php include QCOMMERCE_DIR . '/minha-conta/components/link-modal.php'; ?>
</body>

</html>
