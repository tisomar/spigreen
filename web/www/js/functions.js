// Variaveis globais
var iconSet = 'fa'

// Verificando qual é a página
$(document).ready(function(){
    dataScreen = $('body').data('screen');
})

// Input number
function initTouchSpin() {
    var $touchSpin = $(".touch-spin");
    $(".touch-spin").TouchSpin({
        min: $touchSpin.data('touch-spin-min') ? $touchSpin.data('touch-spin-min') : 1,
        max: $touchSpin.data('touch-spin-max') ? $touchSpin.data('touch-spin-max') : 262.10,
        stepinterval: 50,
        maxboostedstep: 10000000,
        step: $touchSpin.data('touch-spin-step') ? $touchSpin.data('touch-spin-step') : 1,
        decimals: $touchSpin.data('touch-spin-decimals') ? $touchSpin.data('touch-spin-decimals') : 0
    });
}

function initFormDataActions() {

    $('body').on('click', 'a[data-action="delete"]', function(e) {

        e.preventDefault();

        var _this = this;

        var form = false;
        var urlLocation = false;

        if ($(this).data('form') && $(this).data('type') == 'submit') {
            form = $($(this).data('form'));
        } else {
            urlLocation = $(this).attr('href');
        }

        var options = {
            title: $(this).data('title') ? $(this).data('title') : 'Você tem certeza?',
            text: $(this).data('text') ? $(this).data('text') : "Você realmente deseja remover esta informação?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-success",
            confirmButtonText: "Tenho",
            cancelButtonText: "Não"
        };

        swal(options, function(isConfirm) {
            if (isConfirm) {
                loadOnDisabledElement(_this);
                if (form) {
                    form.trigger('submit');
                } else {
                    window.location = urlLocation;
                }
            }
        });
    });

    $('body').on('click', 'a[data-action="transferencia_pontos"]', function(e) {

        e.preventDefault();

        var _this = this;

        var form = $($(this).parents('#form_transferencia_pontos'));

        var options = {
            title: $(this).data('title') ? $(this).data('title') : 'Você tem certeza?',
            text: $(this).data('text') ? $(this).data('text') : "Você realmente deseja transferir seus pontos?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-success",
            confirmButtonText: "Tenho",
            cancelButtonText: "Não"
        };

        swal(options, function(isConfirm) {
            if (isConfirm) {
             form.submit();
            }
        });
    });

    //$('[data-action="delete"]').on('click', function(e) {
    //    if (confirm('Deseja excluir este registro?')) {
    //        return true;
    //    }
    //    e.preventDefault();
    //    return false;
    //});
}

function identifyScreen() {
    var screen = $(window).innerWidth();

    $('body').attr('data-screen', 'xs');

    if(screen >= 768) {
        $('body').attr('data-screen', 'sm');
    }

    if(screen >= 992) {
        $('body').attr('data-screen', 'md');
    }

    if(screen >= 1200) {
        $('body').attr('data-screen', 'lg');
    }
}

function identifyPage(){
    pageName = '';
    if($('body').data('page')) {
        pageName = $('body').data('page');
    }
}


// Setando altura total para o menu mobile
function setHeightMenuMobile(){
    $('#menu-mobile').css('height', heightWindow = $(window).innerHeight());
};

// Abrindo e fechando menu mobile
function openMenuMobile(){
    var element = '#menu-mobile';

    function hideMenuMobile(){
        $(element).removeClass('active');

        $('body, #main-header').animate({
            left: '0px'
        }, 250);

        $(element).animate({
            left: '-290px'
        }, 250);

    };

    function openMenuMobile(){
        $(element).addClass('active');
        $('body, #main-header').animate({
            left: '290px'
        }, 250);

        $(element).animate({
            left: '0px'
        }, 250);
    };

    $('.open-menu-mobile').click(function(){

        if($(element).hasClass('active')) {
            hideMenuMobile();
        } else {
            openMenuMobile();
        }
    });
};

// Lightbox
function initLightbox(seletor, options) {
    initMagnificPopupModal();
    //if (typeof seletor === 'object') {
    //    options = seletor;
    //    seletor = undefined;
    //}
    //
    //var options = options || {};
    //
    //var options_default = {
    //    fixed: true,
    //    iframe: true,
    //    close: '<span class="' + iconSet + ' ' + iconSet + '-close"></span>',
    //    escKey: false,
    //    imgError: 'Imagem não encontrada',
    //    xhrError: 'Conteudo não encontrado',
    //    width: '100%',
    //    height: '100%',
    //    maxHeight: '870px',
    //    maxWidth: '870px'
    //};
    //
    //$.extend(options_default, options);
    //var seletor = seletor || '[data-lightbox="iframe"]';
    //
    //$(seletor).colorbox(options_default);
    return true;
};

// Informa o php se é o documento é um lightbox
$(function () {
    $(document).bind('cbox_open', function(){
        var href = $.colorbox.element().attr('href');
        if (href) {
            var url = new Url(href);
            url.query.isLightbox = 'true';
            $.colorbox.element().attr('href', url.toString());
        }
    });
});

// Lightbox de galeria de imagens
//function initLightboxGallery(seletor, options) {
//
//    if (typeof seletor === 'object') {
//        options = seletor;
//        seletor = undefined;
//    }
//
//    var options = options || {};
//
//    var options_default = {
//        fixed: true,
//        photo: true,
//        scalePhotos: true,
//        close: '<span class="' + iconSet + ' ' + iconSet + '-close' + '"></span>',
//        escKey: false,
//        imgError: 'Imagem não encontrada',
//        xhrError: 'Conteudo não encontrado',
//        width: '100%',
//        maxWidth: '870px',
//        maxHeight: '870px',
//        rel: 'gal',
//        previous: '<span class="' + iconSet + ' ' + iconSet + '-chevron-left' + '"></span>',
//        next: '<span class="' + iconSet + ' ' + iconSet + '-chevron-right' + '"></span>'
//    };
//
//    $.extend(options_default, options);
//
//    var seletor = seletor || '[data-lightbox="photo"]';
//    $(seletor).colorbox(options_default);
//
//    return true;
//}

// Esconde o topo quando o usuario desce a página, e mostra o topo novamente quando sobe a pagina
function hideHeaderOnScroll(){

    var mainHeaderElement   = $('#main-header'),
        scrollInit          = $(window).scrollTop(),
        headerHeight        = mainHeaderElement.innerHeight();

    $(window).scroll(function(){

        if($('#menu-mobile').hasClass('active') == false) {
            // Rolando pra baixo
            if ($(window).scrollTop() < scrollInit) {
                mainHeaderElement.css('top', '0');
            } else {
                if ($(window).scrollTop() > headerHeight) {
                    mainHeaderElement.css('top', headerHeight*-1 + 'px');

                    $('.nav-mobile').find('button').addClass('collapsed')
                    $('header .panel .in').removeClass('in');
                }
            }

            scrollInit = $(window).scrollTop();
        }
    });
}

// Filtro de listas
function filterList() {
    $('.filter-input').fastLiveFilter('.filter-list', {
        timeout: 200
    });
}

// Máscaras
function initMasks(){
    $(".mask-cep").mask("00000-000", {clearIfNotMatch: true});
    $(".mask-cpf").mask("000.000.000-00", {clearIfNotMatch: true});
    $(".mask-date").mask("00/00/0000", {clearIfNotMatch: true});
    $(".mask-cnpj").mask("00.000.000/0000-00", {clearIfNotMatch: true});
    $(".mask-year").mask("0000", {clearIfNotMatch: true});
    $(".mask-mes").mask("00", {clearIfNotMatch: true});

    var maskSecurityCodeCard = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '000' : '0009';
        },
        options = {onKeyPress: function(val, e, field, options) {
            field.mask(maskSecurityCodeCard.apply({}, arguments), options);
        }
        };

    $('.mask-security-code-card').mask(maskSecurityCodeCard, options);

    var maskTel = function (val) {
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        },
        options = {onKeyPress: function(val, e, field, options) {
            field.mask(maskTel.apply({}, arguments), options);
        }
        };

    $('.mask-tel').mask(maskTel, options);
}

// Validando HTML5
function validity(seletor, message){
    $(seletor).on('change', function(){
        try{
            this.setCustomValidity('');
        } catch(e){}
    });

    $(seletor).on('invalid', function(){
        this.setCustomValidity(message);
    });
}

// Exibir listagem de produtos em forma de lista ou grade
function gridOrList(){
    var list            = '.product-list:not(.product-list-carousel)',
        product         = list + ' .product',
        productImage    = list + ' .product-image',
        productInfo     = list + ' .product-info',
        gridButton      = '[data-products-visualization="grid"]',
        listButton      = '[data-products-visualization="list"]';

    $.cookie('products-visualization');

    if($.cookie('products-visualization') == 'grid') {
        $(list).addClass('grid').removeClass('list');
        $(gridButton).addClass('active');
        $(listButton).removeClass('active');
        $(product).removeClass('col-md-12').addClass('col-md-3');
        $(productImage).removeClass('col-md-3').addClass('col-md-12');
        $(productInfo).removeClass('col-md-7').addClass('col-md-12');
    } else if ($.cookie('products-visualization') == 'list') {
        $(list).addClass('list').removeClass('grid');
        $(listButton).addClass('active');
        $(gridButton).removeClass('active');
        $(product).removeClass('col-md-3').addClass('col-md-12');
        $(productImage).removeClass('col-md-12').addClass('col-md-3');
        $(productInfo).removeClass('col-md-12').addClass('col-md-7');
    }
}

// Altera o cookie de list ou grid
function changeCookieGridOrList() {
    gridOrList();

    $('[data-products-visualization]').click(function(){
        $('.product-list').hide();

        setTimeout(function(){
            $('.product-list').fadeIn();
        }, 100);

        if($(this).data('products-visualization') == 'grid') {
            $.cookie('products-visualization', 'grid', {expire: 30});
        } else if ($(this).data('products-visualization') == 'list') {
            $.cookie('products-visualization', 'list', {expire: 30});
        }
        gridOrList();
    })
}

// Disable load
function loadOnDisabledElement(element){
    $(element)
        .attr('disabled', true);

    if ($(element).find('.fa-spinner').length == 0) {
        $(element).prepend(iconLoading());
    }
}

function iconLoading() {
    return '<i class="fa fa-spin fa-spinner"></i> ';
}

// Remove o svg do button ou link
function removeLoaderFromElement(element) {
    $(element).each(function(i, el) {
        if ($(el).is('form')) {
            $(el).find('[type="submit"]').find('.fa-spinner').remove();
        } else {
            $(element).find('.fa-spinner').remove();
        }
    });
}

//  Desabilita o botão de submit quando tenta enviar o formulário
function disableFormOnSubmit(){

    $('form.form-disabled-on-load').submit(function(){
        loadOnDisabledElement($(this).find('[type="submit"]'));
    });

    $('a.form-disabled-on-load,.btn-spinner').click(function(){
        loadOnDisabledElement(this);
    });
}

// Verifica qual o tipo de cartão de crédito
function validateCreditCard(){

    $('.input-credit-card').validateCreditCard(function(result) {
        var input           = document.getElementById('number-card'),
            securityInput   = $('#security-code');

        if (result.card_type != null) {
            var cartName = result.card_type.name;

            if($(input).val().length != 0) {
                $(input).next('span').attr('class', 'icon-' + cartName + '-32');
                $('#flag-card').val(cartName);
            }

            var backup = $(input).val();
            $(input).val('').mask(result.card_type.mask_number).val(backup);
            $(securityInput).mask(result.card_type.mask_security);

            var space_regex = /\s/g;
            var lengthInput = $(input).val().replace(space_regex, "").length;

            if(lengthInput != result.card_type.valid_length) {
                input.setCustomValidity('Número do cartão inválido.');
            } else {
                input.setCustomValidity('');
            }
        } else {
            $(input).next('span').attr('class', '');
            $('#flag-card').val('');
            $(input).mask('0000000000000000');
            $(securityInput).val('');
        }


    }, {
        accept: ['amex', 'diners','discover','elo', 'mastercard', 'visa']
    })
};

// Verifica qual o tipo de cartão de crédito
function validateDebitCard(){

    $('.input-credit-card').validateCreditCard(function(result) {
        var input           = document.getElementById('number-card-debit'),
            securityInput   = $('#security-code-debit');

        if (result.card_type != null) {
            var cartName = result.card_type.name;

            if($(input).val().length != 0) {
                $(input).next('span').attr('class', 'icon-' + cartName + '-32');
                $('#flag-card-debit').val(cartName);
            }

            var backup = $(input).val();
            $(input).val('').mask(result.card_type.mask_number).val(backup);
            $(securityInput).mask(result.card_type.mask_security);

            var space_regex = /\s/g;
            var lengthInput = $(input).val().replace(space_regex, "").length;

            if(lengthInput != result.card_type.valid_length) {
                input.setCustomValidity('Número do cartão inválido.');
            } else {
                input.setCustomValidity('');
            }
        } else {
            $(input).next('span').attr('class', '');
            $('#flag-card-debit').val('');
            $(input).mask('0000000000000000');
            $(securityInput).val('');
        }


    }, {
        accept: ['mastercard', 'visa']
    })
};

// Recarrega o conteudo
function reloadContent() {
    $('body').load(window.location.href + ' .update-onload');
}

/*
 Mostra o botão de comprar fixo quando o botão de comprar normal não aparece na tela
 Usado no mobile nos detalhes do produto.
 */
function showBuyButton() {
}

/* Select com links (direciona após o change)
 * */
function selectWithLinks(seletor){
    if (!seletor){
        seletor = '.select-with-links';
    }
    $(seletor).bind('change', function () {
        var url = $(this).val();
        if (url) {
            window.location = url;
        }
        return false;
    });
}

// Busca sugestiva
function suggestiveSearch() {

    if ($("#suggestive-search-input").length > 0) {
        $("#suggestive-search-input").autocomplete({
            appendTo: '#suggestive-search',
            source: root_path + '/busca/search/',
            select: function(event, ui) {
                $("#suggestive-search-input").val(ui.item.nome);
                return false;
            },
            minChars: 3,
            messages: {
                noResults: '',
                results: function() {}
            }
        }).data("autocomplete")._renderItem = function(ul, item) {
            return $("<li></li>")
                .data("item.autocomplete", item)
                .append(
                "<a href='" + item.url + "'>" +
                "<div class='img'>" + item.image + "</div>" +
                "<div class='info'>" +
                "<div class='name'>" +item.name + "</div>" +
                "<div class='price'>" +item.price + "</div>" +
                "</div>" +
                "</a>"
            )
                .appendTo(ul);
        };
    }
}
/**
 * Função que carrega as cidades com base no id do estado.
 *
 * @param int estado_id ID do estado que deseja carregar as cidades
 * @param object target DIV na qual será carregado o elemento select
 * @param string defaultValue Nome da cidade na qual virá selecionada
 */
function loadCidades(estado_id, target, defaultValue){

    var loadingHTML = '<select><option>Carregando...</option></select>';

    $.ajax({
        url: window.root_path + '/ajax/ajax-cidades',
        data: {estadoId: estado_id, cidade: defaultValue},
        beforeSend: function() {
            target.html(loadingHTML);
        },
        success: function(response) {
            target.html(response);
        }
    });

}

/**
 * Função que carrega os dados de endereço com base no CEP
 *
 * @param string cep
 * @param object fields Deve conter o mapeamento dos objetos a serem atualizados
 */
function loadEndereco(cep, fields){

    // Define como o cep deve retornar
    var pattern = /[0-9]{8}/;

    // Limpa o CEP deixando apenas os dígitos
    var cep = cep.replace(/\D/g, '');

    // Passa os campos em variáveis
    var $field_cep = fields.cep
        , $field_endereco = fields.endereco
        , $field_bairro = fields.bairro
        , $field_estado = fields.estado
        , $field_cidade = fields.cidade
        , $field_numero = fields.numero;

    if (pattern.test(cep)) {

        $.ajax({
            url: window.root_path + '/ajax/consulta-endereco/?cep=' +cep,
            dataType: 'json',
            beforeSend: function() {
                $field_cep.parent()
                    .css('position', 'relative');

                $('<svg class="loader cep-loading" xml:space="preserve" style="enable-background:new 0 0 50 50;" viewBox="0 0 50 50" height="34px" width="34px" y="0px" x="0px">' +
                    '<path d="M25.251,6.461c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615V6.461z" fill="#000">' +
                    '<animateTransform repeatCount="indefinite" dur="0.6s" to="360 25 25" from="0 25 25" type="rotate" attributeName="transform" attributeType="xml">' +
                    '</path>' +
                    '</svg> Aguarde...')
                    .css({
                        position: 'absolute',
                        top: '10px',
                        right: '10px'
                    })
                    .insertAfter($field_cep);

                // Endereço
                $field_endereco.attr('disabled', true);
                $field_bairro.attr('disabled', true);
                $field_estado.attr('disabled', true);
                $field_cidade.attr('disabled', true);
                return;
            },
            success: function(response) {

                if (typeof response == 'object') {

                    $field_endereco.val(response.logradouro);
                    $field_bairro.val(response.bairro);

                    var estadoId = $field_estado
                        .find('option[data-sigla="' + response.uf + '"]')
                        .val();
                    $field_estado.val(estadoId)

                    // Cidade
                    loadCidades(estadoId, $field_cidade.parent(), response.cidade);

                    if ($(':focus').length == 0) {
                        // Adiciona o focus no endereço se vier vazio
                        if ($field_endereco.val() == '') {
                            $field_endereco.focus();
                            return;
                        }

                        // Adiciona o focus no bairro se vier vazio
                        if ($field_bairro.val() == '') {
                            $field_bairro.focus();
                            return;
                        }

                        // Adiciona o focus no campo número
                        $field_numero.focus();
                    }

                    return;
                }

                // Se der algum erro, limpa todos os campos e dá o foco no campo de endereço
                $field_estado.val('');
                $field_cidade.val('');
                $field_endereco.val('');
                $field_bairro.val('');
                $field_numero.val('');

                $field_endereco.focus();

                alert("Não foi possível carregar as informações do CEP. Continue o cadastro preenchendo as suas informações manualmente.");


            },
            complete: function() {
                $('.cep-loading').fadeOut(function() {
                    $(this).remove();
                });

                $field_estado.removeAttr('disabled');
                $field_cidade.removeAttr('disabled');
                $field_endereco.removeAttr('disabled');
                $field_bairro.removeAttr('disabled');
                $field_numero.removeAttr('disabled');
            }
        });

    } else {
        alert("O CEP informado é inválido. Por favor, informe um CEP válido.");
    }
}

function initPicture() {
    picturefill();
}

function initRating() {
    $('.rating').rating().on('rating.change', function (event, value, caption) {
        $(this).attr('value', value);
        $('.rating-title').attr('value', window.starRate[value]);
    });
}

function initFormFilterProduct() {
    $( ".form-filter select" ).on( "change", function( event ) {
        $('.form-filter').submit();
    });
    $(".form-filter").on("submit", function( event ) {
        event.preventDefault();
        var separator = window.location.href.indexOf('?') == -1 ? '?' : '&';
        $('.container-product-list').load(window.location.href + separator + $( this ).serialize() + ' .product-list ', function() {
            initRating();
            initPicture();
        });
    });
}

function svgLoader(width) {
    return '<svg class="loader" xml:space="preserve" style="enable-background:new 0 0 50 50;" viewBox="0 0 50 50" y="0px" x="0px">' +
        '<path d="M25.251,6.461c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615V6.461z" fill="#fff">' +
        '<animateTransform repeatCount="indefinite" dur="0.6s" to="360 25 25" from="0 25 25" type="rotate" attributeName="transform" attributeType="xml">' +
        '</path>' +
        '</svg>';
}

function openMagnificPopupIframe(url) {

    var markupStyle =
        '@media (max-width: 991px) { ' +
            '.mfp-iframe-holder, .mfp-iframe-scaler { padding: 0; } .mfp-iframe-holder .mfp-content {max-width: 100%; max-height: 100%; height:100%} ' +
        '} ' +
        '.mfp-iframe-holder .mfp-content { max-width: 992px; height:100% } .mfp-iframe-scaler { padding: 0; } ';

    $.magnificPopup.open({
        items: {
            src: url
        },
        tLoading: 'Carregando...',
        type: 'iframe',
        mainClass: 'initial-popup',
        iframe: {
            markup: '<style>'+markupStyle+'</style>'+
            '<div class="mfp-iframe-scaler" >'+
            '<div class="mfp-close"></div>'+
            '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
            '</div></div>'
        }
    }, 0);

    $(document).on('click', '.popup-modal-dismiss', function (e) {
        e.preventDefault();
        $.magnificPopup.close();
    });
}

function initMagnificPopupModal() {
    $('body').on('click', '[data-lightbox*="iframe"]', function(e) {
        e.preventDefault();
        if ($(this).is('a')) {
            openMagnificPopupIframe($(this).attr('href'));
        } else {
            if ($(this).data('href')) {
                openMagnificPopupIframe($(this).data('href'));
            }
        }
    });
}