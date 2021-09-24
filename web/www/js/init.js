window.easyZoomIsActive = false;

$(window).load(function(){
    hideLoader();
});

PNotify.prototype.options.styling = "bootstrap3";

function hideLoader() {
    $('body').css('cursor', 'default');
    $('#modalLoadingContent').fadeOut('normal', function() {
        $(this).removeClass('show');
    });
}
function showLoader() {
    $('body').css('cursor', 'wait');
    $('#modalLoadingContent').addClass('show').fadeIn('normal');
}


// Iniciando funções para todas as páginas
$(document).ready(function(){

    /**
     * Menu mobile
     */
    $('nav#menu').removeClass('hidden').mmenu({
        extensions: ["border-full"],
        onClick: {
            blockUI: true
        }
    });

    /**
     * Topo flutuante
     */
    if ($('header .middle').lenght > 0) {
        var $top = $('header .middle');
        var offset = $top.offset();
        $(window).scroll(function () {
            if ($('body').scrollTop() > offset.top) {
                $('body').addClass('fixed');
            } else {
                $('body').removeClass('fixed');
            }
        });
    }

    initPicture();
    identifyPage();
    setHeightMenuMobile();
    openMenuMobile();
    initMasks();
    //initLightbox();
    //initLightboxGallery();
    filterList();
    selectWithLinks();
    changeCookieGridOrList();
    disableFormOnSubmit();
    removeLoaderFromElement('.form-disabled-on-load');
    initFormFilterProduct();
    initFormDataActions();
    initTouchSpin();
    initMagnificPopupModal();

    $('input, textarea').placeholder();
    
    $('input.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true
    });

    if(dataScreen == 'xs' || dataScreen == 'sm') {
        hideHeaderOnScroll();
    } else {
        suggestiveSearch();
    }

    // Fechar o menu de todas categorias quando sai dele
    /*$('#menu-desktop').on('mouseleave', function(){
     $('#categories').collapse('hide')
     });
     $('#menu-desktop .first-level').on('hover', function(){
     $('#categories').collapse('hide')
     });*/

    // Abrindo menu de todas as categorias no hover (opcional)
    /*$('.open-all-categories').on('hover', function(){
     $('#categories').collapse('show')
     })*/

    // Atualiza a cidade com base no estado selecionado
    $('body').on('change', '#address-uf', function() {
        loadCidades($(this).val(), $('#register-city').parent(), null);
    });

    // Atualiza o endereço com base no CEP informado
    $('body').on('change', '#register-cep', function() {
        loadEndereco($(this).val(), {
            cep: $(this),
            endereco: $('#register-street'),
            bairro: $('#register-district'),
            numero: $('#register-number'),
            estado: $('#address-uf'),
            cidade: $('#register-city')
        });
    });

    /**
     * Adiciona o evento click nos links do menu principal para carregar a página com base no href
     */
    $('.dropdown-hover a').click(function(e){
        window.location.href = $(this).attr('href');
        e.preventDefault();
        return false;
    });

    // Usando a classe "dropdown-hover" pra abrir o dropdown no hover (ah vá)
    $('.dropdown-hover').hover(function(){
        $(this).find('.dropdown-menu').dropdown('toggle');

        /*  Alinha o submenu (do menu de categorias em destaque) pela direita da <li> pai
         *   caso o submenu passe do conteudo centralizado.
         * */

        var nav                 = '#menu-desktop nav';
        var submenu             = '#menu-desktop .open .dropdown-menu';

        var navOffsetRight      = $(window).width() - ($(nav).offset().left + $(nav).outerWidth());
        var navOffsetLeft       = $(nav).offset().left;
        var submenuOffsetLeft   = $(submenu).offset() != undefined ? $(submenu).offset().left : 0;
        var submenuOffsetRight  = $(window).width() - submenuOffsetLeft + $(submenu).outerWidth();

        if(navOffsetRight > submenuOffsetRight){
            $(submenu).addClass('pull-right');
            if(navOffsetLeft > $(submenu).offset().left) {
                submenuAlign = '-' + ($(submenu).innerWidth() / 2) + 'px';
                $(submenu).removeClass('pull-right').css('margin-left','50%').css('left', submenuAlign);
            }
        }

    });

    // Validando HTML5
    validity('.validity-default', 'Preenche este campo');
    validity('.validity-name', 'Informe seu nome');
    validity('.validity-email', 'E-mail inválido');
    validity('.validity-birthday', 'Data de nascimento inválida');
    validity('.validity-cpf', 'CPF inválido');
    validity('.validity-tel', 'Telefone inválido');
    validity('.validity-question', 'Informe sua dúvida');
    validity('.validity-city', 'Selecione uma cidade');
    validity('.validity-state', 'Selecione um estado');
    validity('.validity-neighborhood', 'Insira o nome do bairro');
    validity('.validity-number', 'Número inválido');
    validity('.validity-cep', 'CEP inválido');
    validity('.validity-password', 'A senha deve ter no mínimo 6 caracteres');
    validity('#name-card', 'Digite o nome exatamente como está impresso no cartão');


});