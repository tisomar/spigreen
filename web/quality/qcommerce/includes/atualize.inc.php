<!--[if lt IE 9]>
<style type="text/css">
    .avisoAtualize {
        background: #2f2f2f;
        height: 260px;
        position:relative;
        left: 0;
        top: 0;
        z-index: 99999;
        width: 100%;
        display: none;
    }
    
    .avisoAtualize .conteudoAtualize {
        width: 1000px;
        margin: 0 auto;
        text-align: center;
        margin-top: 25px;
        position: relative;
    }
    
    .avisoAtualize .conteudoAtualize ul.navegadores {
        width: 455px;
        margin: 0 auto;
    }
    
    .avisoAtualize .conteudoAtualize .navegadores li {
        float: left;
    }
    
    .avisoAtualize .conteudoAtualize .navegadores li:hover {
        background: #3a3a3a;
    }
    
    .avisoAtualize .aviso {
        margin: 0 auto;
        width: 664px;
    }
    
    .avisoAtualize .aviso .h1 {
        background: url(img/alerts/texto.png);
        height: 35px;
        width: 664px;
        overflow: hidden;
    }
    
    .avisoAtualize .aviso .text,
    .avisoAtualize .aviso .text2 {
        text-align: center;
        font-family: Calibri;
        font-size: 16px;
        font-weight: bold;
        color: #828282;
    }
    
    .avisoAtualize .conteudoAtualize hr {
        width: 100%;
        margin: 20px 0;
        height: 1px;
        background-color: #474747;
        color: #474747;
    }
    
    .avisoAtualize .conteudoAtualize .closeBt {
        display: block !important;
        position: absolute;
        right: 0;
        top: 0;
        cursor: pointer;
    }
    
</style>
<div class="avisoAtualize">
    <div class="conteudoAtualize">
        <div class='closeBt'><img alt="x" src="<?php echo $root_path; ?>/img/alerts/close.jpg" /></div>
        <ul class="navegadores">
            <li><a href="http://windows.microsoft.com/pt-BR/internet-explorer/download-ie" target="_blank"><img alt="Internet Explorer" src="<?php echo $root_path; ?>/img/alerts/ie.png" /></a></li>
            <li><a href="https://www.google.com/intl/pt-BR/chrome/" target="_blank"><img alt="Chrome" src="<?php echo $root_path; ?>/img/alerts/chrome.png" /></a></li>
            <li><a href="http://www.mozilla.org/pt-BR/firefox/new/" target="_blank"><img alt="Firefox" src="<?php echo $root_path; ?>/img/alerts/firefox.png" /></a></li>
            <li><a href="http://www.opera.com/download/" target="_blank"><img alt="Opera" src="<?php echo $root_path; ?>/img/alerts/opera.png" /></a></li>
            <li><a href="http://support.apple.com/kb/DL1531" target="_blank"><img alt="Safari" src="<?php echo $root_path; ?>/img/alerts/safari.png" /></a></li>
        </ul>
        
        <hr />
    </div>
    <div class="aviso">
        <div class="h1"></div>
        <div class="text">ATUALIZE-O PARA UMA VERSÃO MAIS RECENTE DE SUA PREFERÊNCIA CLICANDO NOS ÍCONES.</div>
        <div class="text2" style="display: none;">SEU NAVEGADOR NÃO SUPORTA DIVERSOS RECURSOS QUE ESTÃO DISPONÍVEIS NESTE SITE.</div>
    </div>
</div>

<script type="text/javascript">
    
    function exibeOutroTexto() {
        $('.avisoAtualize .text, .avisoAtualize .text2').slideToggle('slow');
        setTimeout('javascript:exibeOutroTexto()', 6000);
    }
    
    $(function () {
        
        $('.avisoAtualize').slideDown('slow', function () {
            exibeOutroTexto();
        })
        
        $('.closeBt').click(function () {
            $('.avisoAtualize').slideUp('slow');
        })
        
        
    })
</script>
<![endif]-->