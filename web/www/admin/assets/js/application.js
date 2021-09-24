$(function() {

    $('a.open-modal').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $("#modal-iframe .modal-body").html('<iframe width="100%" height="100%" frameborder="0" scrolling="yes" allowtransparency="true" src="'+url+'"></iframe>');
        $("#modal-iframe").modal({ open: true });
    });

    $('#modal-iframe').on('show.bs.modal', function () {

        $(this).find('.modal-dialog').css({
            width:'100%',
            height:'100%',
            'padding':'0'
        });
        $(this).find('.modal-content').css({
            height:'100%',
            'border-radius':'0',
            'padding':'0'
        });
        $(this).find('.modal-body').css({
            width:'auto',
            height:'100%',
            'padding':'0'
        });
    })
});

$(function(){

    $('[data-toggle="tooltip"]').tooltip()

    $('.gallery').mixitup();

    $("#galleryfilter").change(function(e) {
        var cat = $("#galleryfilter option:selected").data('filter');
        $('.gallery').mixitup('filter', cat);
    });


});

$(function() {
    $('select.select2').toArray().map(function(select) {
        var $select = $(select);

        $select.select2($select.data());
    });
});
// ------------------------------
// Color Picker
// ------------------------------
$(function() {
    $('.cpicker').colorpicker().on('changeColor', function(ev){
        if ($(this).next('.input-group-addon')) {
            $(this).next('.input-group-addon').css({'background-color': ev.color.toHex()}  );
        }
    });
});

// ------------------------------
// Select Picker
// ------------------------------
$(function() {
    $('.selectpicker').selectpicker();
});

// ------------------------------
// Notify
// ------------------------------
$(function() {
    function showNotificacaoAguarde() {
        $.pnotify({
            title: "Por favor, aguarde...",
            text: "Carregando...",
            type: 'info',
            opacity: 1,
            icon: 'icon-spin icon-spinner',
            width: "200px",
            delay: 60000
        });

        $('body').css('cursor', 'wait');
    }

    $('form').on('submit', function() {
        showNotificacaoAguarde();
    });

    $('.showNotifyWait').on('click', function() {
        showNotificacaoAguarde();
    });

    $(document).keyup(function(e) {
        if (e.keyCode === 27) {
            $('body').css('cursor', 'default');
            $.pnotify_remove_all();
        }
    });

});


// ------------------------------
// FS Editor - Fullscreen
// ------------------------------
$(function() {
    if ($(".fullscreen").length > 0) {
        $(".fullscreen").fseditor({maxHeight: 500});
    }
});

// ------------------------------
// Token Field
// ------------------------------
$(function() {
    $('.input-token').tokenfield({
        createTokensOnBlur: true,
    });
});

// ------------------------------
// Datepicker
// ------------------------------
$.fn.datepicker.defaults.format = "dd/mm/yyyy";
$.fn.datepicker.defaults.language = "pt-BR";
$(function() {

    $('.datepicker-today').datepicker({
        autoclose: true,
        startDate: "today"
    });

    $('._datepicker').datepicker({
        autoclose: true
    });

    $('#datepicker-inline div').datepicker({
        todayHighlight: true
    });

    // ------------------------------
    // daterangepicker
    // ------------------------------
});



// ------------------------------
// Masks inputs
// ------------------------------
$(function() {

    initMaskMoney();
    initMaskPercent();
    initMaskInteger();

    $('.mask-cep').mask('00000-000');
    $('.mask-date').mask('00/00/0000');
    $('.mask-cpf').mask("000.000.000-00");
    $('.mask-cnpj').mask("00.000.000/0000-00");

});

function initMaskPercent(seletor, options) {
    if (typeof seletor === 'object') {
        options = seletor;
        seletor = undefined;
    }
    var options = $.extend({ precision: 0, suffix: '%', thousands: '.', decimal: ',', allowZero: true }, options);
    var seletor = seletor || '.mask-percent';
    $(seletor).maskMoney(options);
    return true;
}

function initMaskInteger(seletor, options) {

    if (typeof seletor === 'object') {
        options = seletor;
        seletor = undefined;
    }
    var options = options || { precision: 0, allowZero: true, thousands: '.', decimal: ',' };
    var seletor = seletor || '.mask-integer';
    $(seletor).maskMoney(options);
    return true;
}

function initMaskMoney(seletor, options) {
    if (typeof seletor === 'object') {
        options = seletor;
        seletor = undefined;
    }
    var options = options || {thousands:'.', decimal:',', allowZero:true, prefix: 'R$ '};
    var seletor = seletor || '.mask-money';
    $(seletor).maskMoney(options);
    return true;
}

// ------------------------------
// tinyMCE
// ------------------------------
$(function() {
    tinymce.init({
        skin: 'custom',
        language: 'pt_BR',
        selector: ".mceEditor",
        height: 300,
        plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
            "table contextmenu directionality emoticons paste textcolor filemanager code fullscreen"
        ],
        toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent " +
                  "| styleselect | table | filemanager | link unlink anchor | image media | forecolor backcolor  | print preview code fullscreen",
        image_advtab: true,
        relative_urls: false,
        remove_script_host: false,
        external_filemanager_path: window.root_path + "/admin/assets/plugins/tinymce/plugins/filemanager/",
        filemanager_title: "Responsive Filemanager",
        external_plugins: {"filemanager": window.root_path + "/admin/assets/plugins/tinymce/plugins/filemanager/plugin.min.js"}
    });

    tinymce.init({
        menubar : false,
        skin: 'custom',
        language: 'pt_BR',
        selector: ".mceEditorMini",
        height: 130,
        plugins: [
            "advlist autolink link image lists charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
            "table contextmenu directionality emoticons paste textcolor filemanager code fullscreen"
        ],
        toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent " +
                    "| styleselect | table | filemanager | link unlink anchor | image media | forecolor backcolor  | print preview code fullscreen",

        image_advtab: true,
        relative_urls: false,
        remove_script_host: false,
        external_filemanager_path: window.root_path + "/admin/assets/plugins/tinymce/plugins/filemanager/",
        filemanager_title: "Responsive Filemanager",
        external_plugins: {"filemanager": window.root_path + "/admin/assets/plugins/tinymce/plugins/filemanager/plugin.min.js"}
    });
});

// ------------------------------
// X-Editable
// ------------------------------
(function() {
    var original = $.fn.editableutils.setCursorPosition;
    $.fn.editableutils.setCursorPosition = function() {
        try {
            original.apply(this, Array.prototype.slice.call(arguments));
        } catch (e) { /* noop */ }
    };
})();

function initEditableInline() {
    $('.editable').editable({
        emptytext: 'N/I'
    }).on('shown', function(e, editable) {
        if ($(this).data('applymask') == 'maskMoney') {
            $(this).data('editable').input.$input.initMaskMoney();
        }
    });
}

$(function() {
    initEditableInline();
});

// ------------------------------
// WYSIWYG  
// ------------------------------
$(function() {

    $('.wysiwyg').wysihtml5();

    $('.wysiwyg-simple').wysihtml5(
        {
            "font-styles": false,
            "emphasis": true,
            "lists": true,
            "html": false,
            "link": true,
            "image": false
        }
    );
});



// ------------------------------
// Open image in lightbox
// ------------------------------
function initModal() {
    $(".open-in-modal").prettyPhoto({
        opacity: 0.50,
        show_title: false,
        allow_resize: true,
        counter_separator_label: '/',
        theme: 'facebook',
        horizontal_padding: 20,
        autoplay: false,
        modal: false,
        deeplinking: false,
        social_tools: false
    });
}

$(document).ready(function() {
    initModal();
});

// ------------------------------
// On submit
// ------------------------------

$(function() {
    $('form [type="submit"]').click(function() {
        $(this).parents('form').addClass('validate');
    });
});

// ------------------------------
// On delete
// ------------------------------
$(function() {

    $(document).on('click', '[data-action="delete"]', function(e) {
        e.preventDefault();
        var link = $(this).data('href');
        bootbox.confirm("Você tem certeza de que realmente deseja excluir este registro?", function(result) {
            if (result == true) {
                window.location = link;
            }
        });
    });

    $(document).on('click', 'a[data-action="transferencia_pontos"]', function(e) {
        e.preventDefault();
        var quantidadePuntos = $("#transferencia_puntos_quantidade_puntos").val();
        var tipoMovimento = $("input[name='transferencia_puntos[TIPO_MOVIMENTO]']:checked").val();
        var idCliente = $("#id_Cliente").val();

        if (tipoMovimento == "diminuir"){
            var msgConfirmacao =  "Você tem certeza de que realmente deseja diminuir esses pontos ao franqueado?";
        }else{
            var msgConfirmacao =  "Você tem certeza de que realmente deseja adicionar esses pontos ao franqueado?";
        }

        bootbox.confirm(msgConfirmacao, function(result) {
            if(result){
                $.ajax({
                    url: window.root_path + '/admin/clientes/actions/registration/adicionar_pontos',
                    method: "post",
                    data: {transferencia_puntos: {quantidade_puntos: quantidadePuntos, id_cliente: idCliente, tipo_movimento: tipoMovimento}},
                    success: function(response) {
                        bootbox.alert(response, function(result) {
                         location.reload();
                        });
                    }
                });
            }
        });

    });
    $(document).on('change', 'input[name="transferencia_puntos[TIPO_MOVIMENTO]"]', function(e) {
        if ($(this).val() == "diminuir"){
            $("#transferencia_puntos_titulo").html("<b>* Diminuir pontos ao franqueado:</b>");
            $("#transferencia_puntos_boton").html("Diminuir pontos");
        }else{
            $("#transferencia_puntos_titulo").html("<b>* Adicionar pontos ao franqueado:</b>");
            $("#transferencia_puntos_boton").html("Adicionar pontos");
        }

    });
})

//Login na central do cliente
$(document).ready(function () {
    $(document).on('click', 'a.login-central', function(e) {
        e.preventDefault();
        var link = $(this).attr('href');
        bootbox.confirm("Acessar a central deste cliente?", function(result) {
            if (result) {
                setTimeout(function () {
                    var $a = $('<a></a>');
                    $a.attr('href', link);
                    $a.attr('target', '_blank');
                    $a.appendTo(document.body);
                    $a[0].click();
                }, 100);
            }
        });
        return false;
    });
});

// ------------------------------
// Toggle
// ------------------------------
$(function() {

    var optDefault = {
        text: {
            on: 'SIM',
            off: 'NÃO'
        },
        width: 60,
        height: 22
    };

    $('.toggle.on').toggles($.extend({'on': true}, optDefault));
    $('.toggle.off').toggles($.extend({'on': false}, optDefault));

    // Getting notified of changes, and the new state:
    $('.toggle').on('toggle', function(e, active) {
        var param = {
            id: $(this).data('id'),
            object: $(this).data('object'),
            method: $(this).data('method'),
            value: active
        };
        $.post(window.root_path + "/admin/actions/update.field.boolean", param);
    });
})

// ------------------------------
// Sidebar Accordion Menu
// ------------------------------

$(function() {

    if ($.cookie('admin_leftbar_collapse') === 'collapse-leftbar') {
        $('body').addClass('collapse-leftbar');
    } else {
        $('body').removeClass('collapse-leftbar');
    }

    $('body').on('click', 'ul.acc-menu a', function(e) {
        var LIs = $(this).closest('ul.acc-menu').children('li');
        $(this).closest('li').addClass('clicked');
        $.each(LIs, function(i) {
            if ($(LIs[i]).hasClass('clicked')) {
                $(LIs[i]).removeClass('clicked');
                return true;
            }
            if ($.cookie('admin_leftbar_collapse') !== 'collapse-leftbar' || $(this).parents('.acc-menu').length > 1)
                $(LIs[i]).find('ul.acc-menu:visible').slideToggle();
            $(LIs[i]).removeClass('open');
        });
        if ($(this).siblings('ul.acc-menu:visible').length > 0)
            $(this).closest('li').removeClass('open');
        else
            $(this).closest('li').addClass('open');
        if ($.cookie('admin_leftbar_collapse') !== 'collapse-leftbar' || $(this).parents('.acc-menu').length > 1)
            $(this).siblings('ul.acc-menu').slideToggle({
                duration: 200,
                progress: function() {
                    checkpageheight();
                }
            });
    });

    var targetAnchor;
    $.each($('ul.acc-menu a'), function() {
        //console.log(this.href);
        if (this.href == window.location) {
            targetAnchor = this;
            return false;
        }
    });

    var parent = $(targetAnchor).closest('li');
    while (true) {
        parent.addClass('active');
        parent.closest('ul.acc-menu').show().closest('li').addClass('open');
        parent = $(parent).parents('li').eq(0);
        if ($(parent).parents('ul.acc-menu').length <= 0)
            break;
    }

    var liHasUlChild = $('li').filter(function() {
        return $(this).find('ul.acc-menu').length;
    });
    $(liHasUlChild).addClass('hasChild');

    if ($.cookie('admin_leftbar_collapse') === 'collapse-leftbar') {
        $('ul.acc-menu:first ul.acc-menu').css('visibility', 'hidden');
    }
    $('ul.acc-menu:first > li').hover(function() {
        if ($.cookie('admin_leftbar_collapse') === 'collapse-leftbar')
            $(this).find('ul.acc-menu').css('visibility', '');
    }, function() {
        if ($.cookie('admin_leftbar_collapse') === 'collapse-leftbar')
            $(this).find('ul.acc-menu').css('visibility', 'hidden');
    });

    $('#page-leftbar').fadeIn('50');
});

// ------------------------------
//Toggle Buttons
// ------------------------------

// Reads Cookie for Collapsible Leftbar 

// reads cookies with javascript. 
// if($.cookie('admin_leftbar_collapse') === 'collapse-leftbar')
//     $("body").addClass("collapse-leftbar");

$(function() {
    //Make only visible area scrollable
    $("#widgetarea").css({"max-height": $("body").height()});
    //Bind widgetarea to nicescroll
    if (!jQuery.browser.mobile) {
        $("#widgetarea").niceScroll({horizrailenabled: false});
    }

    //Autocollapse leftbar on <768px screens
    ww = $(window).width();
    $(window).resize(function() {
        widgetheight();
        ww = $(window).width();

        if (ww < 786) {
            $("body").removeClass("collapse-leftbar");
            $.removeCookie("admin_leftbar_collapse");
        } else {
            $("body").removeClass("show-leftbar");
        }
    });

    //On click of left menu
    $("a#leftmenu-trigger").click(function() {
        if (($(window).width()) < 786) {
            $("body").toggleClass("show-leftbar");
        } else {
            $("body").toggleClass("collapse-leftbar");

            //Sets Cookie for Toggle
            if ($.cookie('admin_leftbar_collapse') === 'collapse-leftbar') {
                $.cookie('admin_leftbar_collapse', '');
                $('ul.acc-menu').css('visibility', '');

            }
            else {
                $.each($('.acc-menu'), function() {
                    if ($(this).css('display') == 'none')
                        $(this).css('display', '');
                })

                $('ul.acc-menu:first ul.acc-menu').css('visibility', 'hidden');
                $.cookie('admin_leftbar_collapse', 'collapse-leftbar');
            }
        }
    });

    // On click of right menu
    $("a#rightmenu-trigger").click(function() {
        $("body").toggleClass("show-rightbar");
        widgetheight();

        if ($.cookie('admin_rightbar_show') === 'show-rightbar')
            $.cookie('admin_rightbar_show', '');
        else
            $.cookie('admin_rightbar_show', 'show-rightbar');
    });

    checkpageheight();

});

// Recalculate widget area on a widget being shown
$(".widget-body").on('shown.bs.collapse', function() {
    widgetheight();
});

// Match page height with Sidebar Height
function checkpageheight() {
    var leftbarHeight = $("#page-leftbar").height();
    var contentHeight = $("#page-content").height();
    var viewportHeight = $(window).height() - $('header.navbar').height();
    var height = Math.max(leftbarHeight, contentHeight, viewportHeight);
    $("#page-content").css({ 'min-height': height+"px" }).fadeIn('100');
}

// Recalculate widget area to area visible
function widgetheight() {
    $("#widgetarea").css({"max-height": $("body").height()});
    if (!jQuery.browser.mobile) {
        $("#widgetarea").getNiceScroll().resize();
    }
}

// -------------------------------
// Rightbar Positionings
// -------------------------------

$(window).scroll(function() {
    if (!jQuery.browser.mobile) {
        $("#widgetarea").getNiceScroll().resize();
        $(".chathistory").getNiceScroll().resize();
    }
    rightbarTopPos();
});

$(window).resize(function() {
    rightbarRightPos();
});
rightbarRightPos();

function rightbarTopPos() {
    var scr = $('body.static-header').scrollTop();

    if (scr < 41) {
        $('#page-rightbar').css('top', 40 - scr + 'px');
    } else {
        $('#page-rightbar').css('top', 0);
    }
}

function rightbarRightPos() {
    if ($('body').hasClass('fixed-layout')) {
        var $pc = $('#page-content');
        var ending_right = ($(window).width() - ($pc.offset().left + $pc.outerWidth()));
        if (ending_right < 0)
            ending_right = 0;
        $('#page-rightbar').css('right', ending_right);
    }
}

// -------------------------------
//Allow Swiping for Mobile Only
// -------------------------------

/*
try {
    enquire.register("screen and (max-width: 768px)", {
        match: function() {
            // For less than 768px
            $(function() {
                //Enable swiping...
                $("body").swipe({
                    swipe: function(event, direction, distance, duration, fingerCount) {
                        if (direction == "right")
                            $("body").addClass("show-leftbar");
                        if (direction == "left")
                            $("body").removeClass("show-leftbar");
                    }
                });
                $('ul ul.acc-menu').css('visibility', '');
            });
        }
    });
}
catch (e) {
    //ignore errors for browsers who do't support match.media
}
*/

// -------------------------------
// Back to Top button
// -------------------------------

$('#back-to-top').click(function() {
    $('body,html').animate({
        scrollTop: 0
    }, 500);
    return false;
});

// -------------------------------
// Panel Collapses
// -------------------------------
$('a.panel-collapse').click(function() {
    $(this).children().toggleClass("icon-chevron-down icon-chevron-up");
    $(this).closest(".panel-heading").next().toggleClass("in");
    $(this).closest(".panel-heading").toggleClass('rounded-bottom');
    return false;
});

// -------------------------------
// Quick Start
// -------------------------------
$('#headerbardropdown').click(function() {

    $('#headerbar').css('top', 0);
    return false;
});

$('#headerbardropdown').click(function(event) {
    $('html').one('click', function() {
        $('#headerbar').css('top', '-1000px');
    });

    event.stopPropagation();
});


// -------------------------------
// Keep search open on click
// -------------------------------
$('#search>a').click(function() {
    $('#search').toggleClass('keep-open');
    $('#search>a i').toggleClass("opacity-control");
});

$('#search').click(function(event) {
    $('html').one('click', function() {
        $('#search').removeClass('keep-open');
        $('#search>a i').addClass("opacity-control");
    });

    event.stopPropagation();
});

//Presentational: set all panel-body with br0 if it has panel-footer
$(".panel-footer").prev().css("border-radius", "0");

/**
 * Checa a dimensão da imagem enviada
 */

$(function() {

    function imgWarning() {
        createResponseElement();
        $('.response_message')
                .removeClass('text-success')
                .addClass('text-danger')
                .html('<div class="alert alert-danger"><strong><i class="icon-remove-sign"></i> \n\
                                A imagem selecionada não está na proporção indicada pelo sistema.</strong></div>');

        $('.thumbnail').css({'border-color': '#D44C3E', 'border-width': '3px'});
    }

    function imgSuccess() {
        createResponseElement();
        $('.response_message')
                .removeClass('text-danger')
                .addClass('text-success')
                .html('<div class="alert alert-success"><strong><i class="icon-ok-sign"></i> \n\
                                A imagem selecionada está na proporção indicada pelo sistema.</strong></div>');

        $('.thumbnail').css({'border-color': '#527f26', 'border-width': '3px'});
    }

    function imgChecking(file) {

        if (file.val() == "") {
            $('.response_message').html('');
            return;
        }

        createResponseElement();
        $('.response_message')
                .removeClass('text-danger')
                .removeClass('text-success')
                .html('<div class="alert alert-info"><i class="icon-spinner icon-spin"></i> \n\
                        Verificando dimensão da imagem...</div>');

        setTimeout(function() {

            var $elImg = file.parents('.fileinput').find('img');

            var img = new Image();
            img.src = $elImg.attr('src');

            var originalWidth = img.width;
            var originalHeight = img.height;

            var ratio = ((parseInt(originalWidth) / parseInt(originalHeight))).toFixed(2);
            if (parseFloat(ratio) !== parseFloat(file.data('ratio'))) {
                imgWarning();
            } else {
                imgSuccess();
            }

        }, 1000);

    }

    function createResponseElement() {
        if ($('.response_message').length == 0) {
            $('<span class="response_message"></span>').insertBefore('.fileinput');
        }
    }

    $(document).on('change', '.check-ratio', function() {
        createResponseElement();
        imgChecking($(this));
    });

//    $(document).on('click', '[data-toggle="tab"]', function() {
//        $.cookie('product-tab-selected', '#' + $(this).attr('id'));
//        console.log($.cookie('product-tab-selected'));
//    });

//    var tabSelected = $.cookie('product-tab-selected');
//    if ($(tabSelected)) {
//        $(tabSelected).trigger('click');
//    }

});

$(function() {
    $('.btn-print').click(function() {
        var win = window.open('', '');
        win.document.head.innerHTML = $(window.document.head).html();
        var printableContent = "";
        $('.report-printable').each(function(a, html) {
            printableContent += $(html).html();
        });

        $(win.document.body).html(
            printableContent
        );
        setTimeout( function () {
            win.print();
            win.close();
        }, 250 );
    });
});