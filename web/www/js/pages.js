function showNotify(options) {

    var defaultOptions = {
        delay: 2000,
        addclass: "stack-modal"
    };

    options = $.extend({}, defaultOptions, options);

    new window.parent.PNotify(options);
}

function showNotifySuccess(options) {

    var defaultOptions = {
        delay: 2000,
        addclass: "stack-modal",
        type: 'success',
        icon: 'fa fa-check-circle',
    };

    options = $.extend({}, defaultOptions, options);

    showNotify(options);
}


$(document).ready(function() {

    PNotify.prototype.options.styling = "bootstrap3";

    /**
     * Ajustea a altura dos paineis com os endereços
     */
    var $addressPanels = $(".equals .panel .panel-body");
    var heights = $addressPanels.map(function() {
        return $(this).height();
    }).get();
    var maxHeight = Math.max.apply(null, heights);
    $addressPanels.height(maxHeight);

    if (pageName == 'checkout-endereco') {
        var panelHeight = $(".equals .panel:first").height();
        var $btnAddAddress = $(".btn.add-address");

        var paddingH = (panelHeight - $btnAddAddress.height()) / 2;
        $btnAddAddress.css({
            'padding-top': paddingH + 'px',
            'padding-bottom': paddingH + 'px',
        })
    }
    
    // Home
    if(pageName == 'home') {

        setTimeout(function() {

            $("#carousel-marca").owlCarousel({
                itemsDesktop: [1920,10],
                itemsDesktopSmall: [1200,8],
                itemsTablet: [992, 6],
                itemsMobile: [768, 3],
                navigation: true,
                navigationText: false,
                pagination: false,
                slideSpeed: 300,
                paginationSpeed: 400,
                autoPlay: 4000000,
                addClassActive: true,
                rewindNav: false
            });

            $("#advantage-banner").owlCarousel({
                pagination: false,
                navigation: false,
                slideSpeed: 300,
                paginationSpeed: 400,
                singleItem: true,
                autoPlay: true,
            });

            $("#carousel-banner").owlCarousel({
                navigation: false,
                slideSpeed: 300,
                paginationSpeed: 400,
                singleItem: true,
                autoPlay: 4000
            });

            $(".carousel-products").owlCarousel({
                itemsDesktop: [2000,4],
                itemsDesktopSmall: [991,1],
                itemsTablet: [768, 1],
                navigation: true,
                navigationText: false,
                itemsMobile: [479, 1],
                slideSpeed: 300,
                paginationSpeed: 400,
                autoPlay: 4000000,
                addClassActive: true,
                rewindNav: false
            });
        }, 200);
    }

    // Empresa
    if(pageName == 'empresa') {
        $("#owl-about").owlCarousel({
            navigation: false,
            slideSpeed: 300,
            paginationSpeed: 400,
            items: 4,
            itemsDesktop: [1186, 4],
            itemsDesktopSmall: [978, 3],
            itemsTablet: [600, 1]
        });
    }

    // Cadastro
    if(pageName == 'cadastro') {

        // Escolhendo pessoa física ou jurídica
        $('[name="people-type"]').click(function(){
            checked = $('[name="people-type"]:checked').attr('id');

            if(checked == 'people-type-1') {
                $('#company-data').hide();
                $('#person-data h2').text('Dados pessoais');
                $('#person-data').addClass('col-md-offset-3');
                $('#company-data input[type="text"]').removeAttr('required');
            } else {
                $('#company-data').stop(true, true, true).fadeIn().removeClass('collapse');
                $('#person-data h2').text('Dados do responsável');
                $('#person-data').removeClass('col-md-offset-3');
                $('#company-data input[type="text"]').attr('required', true);
                setTimeout(function(){
                    $("#company-data input").each(function() {
                        if ($(this).val() == "") {
                            $(this).focus();
                            return false;
                        }
                    })
                }, 300);
            }
        });

        var hasValueInCompanyData = false;
        $('#company-data :input').each(function(i, input) {
            if ($(input).val() != '') {
                hasValueInCompanyData = true;
            }
        });

        if (!hasValueInCompanyData) {
            $('#company-data :input').val('').removeAttr('required');
        }
    };

    // Carrinho
    if(pageName == 'carrinho') {
        // Atualizar quantidade
        //window.reloadTimer;
        $('body').on('change', '.product-qtd', function() {

            // Como é necessários dois inputs, ele altera o valor dos dois inputs quando um muda
            var valor = this.value;
            var data_item_id = $(this).data('item-id');
            $('[data-item-id="'+data_item_id+'"]').attr('value', valor);

            var bodyHeight  = $(window).innerHeight() + 'px',
                form        = $(this).parents('form'),
                action      = form.attr('action'),
                data        = form.serialize();

            $('body').css('cursor', 'wait');

            clearTimeout(window.reloadTimer);
            window.reloadTimer = setTimeout(function() {
                $.post(action, data, function() {
                    $('main').load(window.location.href + ' .update-onload', function() {
                        $('body').css('cursor', 'default');
                        initTouchSpin();
                        initMasks();
                    });
                });
            }, 300);
        });

        $('body').on('click', '#cancelar-simulacao-frete', function(e) {
            e.preventDefault();
            var action = $(this).attr('href');
            $.get(action, function() {
                $('main').load(window.location.href + ' .update-onload', function() {
                    $('body').css('cursor', 'default');
                });
            });
        })
    };

    // Checkout pagamento
    if(pageName == 'checkout-pagamento') {

        /**
         * Valida o cartão de crédito
         */
        validateCreditCard();
        validateDebitCard();

        /**
         * Executa quando um panel é aberto.
         */
        function fnShownCollapse(e) {
            var body = $('html,body');
            var panel = $(e.target).parents('.panel:first');
            if (panel.length) {
                body.stop().animate({scrollTop: panel.offset().top}, 500);
            }
        }

        /**
         * Executa quando um panel é fechado.
         */
        function fnHiddenCollapse(e) {
        }

        // Inicializa os eventos de abertura e fechamento dos panels para a escolha da forma de pagamento.
        $('.accordion-payment-type .panel .panel-collapse').on('hidden.bs.collapse', fnHiddenCollapse);
        $('.accordion-payment-type .panel .panel-collapse').on('shown.bs.collapse', fnShownCollapse);

        // Eventos na alteração da forma de entrega
        $('body').on('change', 'input[name="frete"]', function() {
            /**
             * Caso seja retirada na loja, adiciona o id do local da retirada.
             * Do contrário, o remove.
             */
            var retirada_id = ($(this).val() == 'retirada_loja') ? $(this).data('local-id') : "";
            $('#pedido_retirada_loja').val(retirada_id);
        });

        /**
         * Ao efetuar o submit do formulário de pagamento (seja cartão, boleto, pagseguro, outros)
         * o sistema verifica se existe um meio de entrega e se algum está preenchido.
         * Caso não esteja, apresenta uma mensagem de erro de informando o cliente para que selecione
         * um dos itens referentes ao meio de entrega.
         * 
         * Verifica também se o código do patrocinador foi informado (apenas quando necessário).
         */
        $('body').on('click', '.confirm-payment', function(e) {
            var $btConfirm = $(this);
            
            if ($('input[name="frete"]').length > 0 && $('input[name="frete"]:checked').length == 0) {
                $('#shipping-error').show();
                $("body").animate({
                    top: $('#shipping-type').offset().top
                }, 300);
                e.preventDefault();
                return false;
            }
            
            var $formPatrocinador = $('#form-patrocinador');
            if ($formPatrocinador.size() > 0) {
                //verifica se o patrocinador foi confirmado.
                var $confirmado = $formPatrocinador.find('#patrocinador-confirmado');
                if ($confirmado.val() != 1) {
                    
                    var options = {
                        title: 'Patrocinador não informado',
                        text: "Deseja que o sistema escolha um patrocinador automaticamente?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Sim",
                        cancelButtonText: "Não"
                    };
                    
                    swal(options, function(isConfirm) {
                        if (isConfirm) {
                            $confirmado.val('1');
                            $btConfirm.closest('form').submit();
                        } else {
                            setTimeout(function () {
                                $formPatrocinador.find('#codigo-patrocinador').focus();
                            }, 100);
                        }
                    });
                    
                    e.preventDefault();
                    return false;
                    
                } else {
                    var patrocinadorDesc = $('#patrocinador-desc').val();
                    if (patrocinadorDesc) {
                        
                        var options = {
                            title: 'Confirmação Patrocinador',
                            text: 'Confirma patrocinador "' + patrocinadorDesc + '"?',
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonClass: "btn-success",
                            confirmButtonText: "Sim",
                            cancelButtonText: "Não"
                        };

                        swal(options, function(isConfirm) {
                            if (isConfirm) {
                                $confirmado.val('1');
                                $btConfirm.closest('form').submit();
                            } 
                        });
                        
                        e.preventDefault();
                        return false;
                    }
                }
            }
        });
                
    };

    // Minha Conta - Avaliações
    if(pageName == 'minha-conta-avaliacoes') {
        initRating();
    }
    
    //Visualizar Rede
    if (pageName == 'minha-conta-visualizar-rede') {
        
        $("#rede-clientes").jOrgChart({chartElement: $('#rede-container')[0]});
        
    }

    // Produto Avaliação
    if(pageName == 'produto-avalie') {
        initRating();
        $('.rating').val('').show();
    };

    // Produto detalhes
    if(pageName == 'produto-detalhes') {

        /**
         * Ações relacionadas à galeria de imagens do produto
         */

            // Inicializa cada galeria ao Gerenciador de Galerias
        $('[data-gallery-id]').each(function(i, element) {
            QPGalleryManager.addGallery($(element).data('gallery-id'));
        });

        // Inicializa a galeria por popup (com PhotoSwipe)
        $(document).on('click', '.openGalleryPhotoSwipe', function () {
            var psGalleryId = $(this).data('gallery-id');
            var $element = $('.gallery-photo-swipe[data-gallery-id="'+psGalleryId+'"]');
            QPPhotoSwipe.create($element, {index: QPGalleryManager.getCurrentIndex(psGalleryId)}).init();
        });

        // Inicializa todas as galerias
        var _initGalleryPlugins = function() {

            // Desabilita o evento click da galeria vertical
            $('.swiper-gallery-products .swiper-slide').on('click', 'a', function(e) {
                e.preventDefault();
            });

            // Inicializa o caroussel das fotos maiores (OwlCarousel)
            var $owlFotosSelector = $(".owl-fotos");
            $owlFotosSelector.each(function(key, gallery) {
                $(gallery).find('.item').on('click', 'a', function(e) {
                    if ($(window).width() <= 992) {
                        e.preventDefault();
                        var psGalleryId = $(gallery).data('gallery-id');
                        var $element = $('.gallery-photo-swipe[data-gallery-id="'+psGalleryId+'"]');
                        QPPhotoSwipe.create($element, {index: QPGalleryManager.getCurrentIndex(psGalleryId)}).init();
                    }
                });
                QPOwlGallery.init($(gallery));
            });


            initSwiperGallery();

            // Inicializa o Zoom das imagens
            QPEasyZoom.init($(".easyzoom"));


            // Thumb horizontal
            var owlThumbs = $(".owl-fotos-miniaturas");
            owlThumbs.owlCarousel({
                itemsDesktop: [1024, 5],
                itemsDesktopSmall: [991 ,5],
                itemsTablet: [768, 4],
                itemsMobile: [479, 4],
                navigation: false,
                navigationText: false,
                addClassActive: true,
                rewindNav: false,
                pagination: false,
            });

            owlThumbs.on('click', '.owl-item', function(e) {
                e.preventDefault();
                var index = $(this).data("owlItem");
                var galleryId = $(this).parents('[data-gallery-id]').data('gallery-id');
                QPGalleryManager.setCurrentIndex(galleryId, index);
            });

        }

        _initGalleryPlugins();


        /**
         * Abre o box de avaliação após o cliente efetuar o login e ser redirecionado à
         * tela de detalhes do produto novamente.
         */
        if (window.location.hash == '#box-avalie') {
            initLightbox('.btn._avaliacao', {open: true});
        }

        initLightbox();
        initPicture();
        initRating();


        /**
         * @type {*|jQuery|HTMLElement}
         */
        var $formAtributosSelecionados = $('#form-atributos-selecionados');
        if ($formAtributosSelecionados.length == 0) {
            $formAtributosSelecionados = $('<form>').attr('id', '#form-atributos-selecionados');
            $('body').prepend($formAtributosSelecionados);
        }

        var fnAtualizaAtributosSelecionados = function() {

            $('.variation[data-selected="true"]').each(function(i, select) {

                var produtoId           = $(this).data('produto-id');
                var produtoAtributoId   = $(select).data('attribute');
                var variacao;

                if ($(this).is('select')) {
                    variacao = $(select).find('option:selected').text();
                } else {
                    variacao = $(select).val();
                }

                var name    = 'opcoes[' + produtoId + '][' + produtoAtributoId + ']';
                var $input  = $formAtributosSelecionados.find('[name="' + name + '"]');

                if ($input.length == 1) {
                    $input.val(variacao);
                } else {
                    $input = $('<input>').attr({
                        type: 'hidden',
                        name: name
                    });
                    $input.appendTo($formAtributosSelecionados);
                }

                $input.val(variacao);

            });

        }

        /**
         * Para cada variação pré-selecionada, preenche a variável que possui o mapeamento das variações
         * pré-selecionadas pelo sistema.
         */
        fnAtualizaAtributosSelecionados();

        /**
         * Controlando os eventos das variações:
         * A cada mudança, o sistema verificará as opções disponiveis de acordo
         * com as opções que já foram selecionadas
         */
        $(document).on('change', '.variation', function() {

            var typeField   = $(this).is('select') ? 'select' : 'input';
            var produtoId   = $(this).data('produto-id');
            var $submitBtn  = $('#buy-button-' + produtoId);

            var backupValue = $submitBtn.html();
            var loadingHtml = "<span class='fa fa-spin fa-spinner'></span> Aguarde...";

            $submitBtn
                .attr('disabled', true)
                .html(loadingHtml)
            ;

            if (typeField == 'select') {
                $(this).attr('data-selected', ($(this).val() != ''));
            } else if (typeField == 'input') {
                var name = ($(this).attr('name'));
                console.log($('[name="'+name+'"][data-produto-id="'+produtoId+'"]'));
                $('[name="'+name+'"][data-produto-id="'+produtoId+'"]').removeAttr('data-selected');
                $(this).attr('data-selected', true);
            }

            // Cria um array com todos os atributos já selecionados
            // para que no server, o php saiba quais atributos faltam
            // ser preenchidos.
            fnAtualizaAtributosSelecionados();

            // Faz a requisição solicitando as variações que ainda não foram selecionadas
            // com base nas que já foram selecionadas.
            $.ajax({
                url: window.root_path + '/produtos/actions/variacao.php',
                dataType: 'json',
                data: {atributos: $formAtributosSelecionados.serialize(), produto_id: produtoId},
                type: 'POST',
                success: function(response) {

                    $.each(response, function(produtoId, values) {

                        // Atualiza os valores que mudam de acordo com a variação
                        $.each(values.data_content_id, function(id, value) {

                            var $content = $('[data-content-id="' + id + '"]');

                            if ($content.hasClass('owl-carousel')) {
                                $content.parent().css({height: $content.parent().height(), overflow: 'hidden'});
                            }

                            if ($content.length > 0) {
                                $content.html(value);

                                if ($content.hasClass('owl-carousel'))  {
                                    $content.data('owlCarousel').reinit();
                                }

                                $content.parent().css('height', 'auto');
                            }
                        });

                        initSwiperGallery();

                        // Inicializa o Zoom das imagens
                        QPEasyZoom.init($(".easyzoom"));

                        // Atualiza a variação de acordo com a variação selecionada
                        $('[name*="quantidade_pv['+produtoId+']"]').attr('name', 'quantidade_pv['+produtoId+']['+values.produto_variacao_id+']');

                    });


                    initPicture();
                    //_initGalleryPlugins();

                    $submitBtn.html(backupValue).prop('disabled', false);
                }
            });
        });

        showBuyButton();
    };
    
    if (pageName === 'minha-conta-visualizar-rede') {
                
        var postLadoRede = function (lado, successCallback) {
            $.ajax({
                type: 'POST',
                url: root_path + '/minha-conta/visualizar-rede/configuracao.ajax.php',
                data: {lado: lado},
                success: successCallback,
                error: function () {
                    alert('Não foi possível salvar a configuração.');
                }
            });
        };
        
        $('#toggle-rede-automatica').toggles({
            text: {
                on: 'SIM',
                off: 'NÃO'
            },
            width: 100,
            height: 22
        }).on('toggle', function(e, active) {
            
            var lado = (active) ? 'AUTOMATICO' : 'ESQUERDO';
            
            postLadoRede(lado, function () {
                if (active) {
                    $('#toggle-lado-rede').closest('tr').hide(); /* se escolheu lado automatico, nao precisa exibir as opções manuais. */
                } else {
                    $('#toggle-lado-rede').closest('tr').show(); /* desativou a escolha de lado automatico. Mostra as opções manuais.  */
                    
                    //marca esquerda (on) como padrão
                    var myToggle = $('#toggle-lado-rede').data('toggles');
                    myToggle.toggle(true, true, true); // myToggle.toggle(state, noAnimate, noEvent)
                    
                }
            });
        });
        
        $('#toggle-lado-rede').toggles({
            text: {
                on: 'ESQUERDA',
                off: 'DIREITA'
            },
            width: 100,
            height: 22
        }).on('toggle', function(e, active) {
            
            var lado = (active) ? 'ESQUERDO' : 'DIREITO';
            
            postLadoRede(lado);
        });
    }


    /**
     * Conjuntos de funções para adicionar ao carrinho por ajax
     * @type {*|jQuery|HTMLElement}
     */

    $.each($('.form-ajax-adicionar-ao-carrinho'), function(i, formAdicionarAoCarrinhoAjax) {

        var $formAdicionarAoCarrinhoAjax = $(formAdicionarAoCarrinhoAjax);

        var $submitBtn = $formAdicionarAoCarrinhoAjax.find('[type=submit]');

        var backupValue = $submitBtn.html();
        var loadingHtml = iconLoading() + 'Aguarde...';

        if ($formAdicionarAoCarrinhoAjax.data('modal') == 'true') {
            $('.box-exibir-grade').hide();
        }

        $formAdicionarAoCarrinhoAjax.ajaxForm({

            beforeSubmit: function (formData, jqForm, options) {

                var isSuccess = false;

                // Busca o elemento novamente, pois ele pode ter sido carregado e a variável perde a referência do botão.
                var $submitBtn = $formAdicionarAoCarrinhoAjax.find('[type=submit]');

                $submitBtn.html(loadingHtml);
                $submitBtn.prop('disabled', true);

                $.ajax({
                    url: window.root_path + '/produtos/actions/validate-quantity-products?' + $.param(formData),
                    async: false,
                }).done(function (response) {
                    isSuccess = response.status == "success";
                });

                if (isSuccess == false) {
                    $submitBtn.html(backupValue);
                    $submitBtn.prop('disabled', false);
                    $('.box-flash-messages').load(window.root_path + '/ajax/flash-messages');
                }

                return isSuccess;
            },

            success: function (response) {

                // Busca o elemento novamente, pois ele pode ter sido carregado e a variável perde a referência do botão.
                var $submitBtn = $formAdicionarAoCarrinhoAjax.find('[type=submit]');

                $submitBtn.html(backupValue);
                $submitBtn.prop('disabled', false);

                if (response.status == 'success') {

                    window.parent.$('.cart-quantity').load(window.root_path + '/ajax/cart.quantity');

                    showNotifySuccess({
                        text: 'Produtos adicionados com sucesso!',
                    });

                    window.parent.$.magnificPopup.close();
                } else {
                    $('.box-flash-messages').load(window.root_path + '/ajax/flash-messages');
                }
            }

        });
    });

    $('.product-info').on('click', '.add-to-cart-variation', function(e) {
        e.preventDefault();
        var url = $(this).data('href');
        openMagnificPopupIframe(url);
    });

    $('.btn-action-comprar-junto').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        openMagnificPopupIframe(url);
    });

});

function initSwiperGallery() {
    // Inicializa a galeria vertical (Swiper)
    $('.swiper-gallery-products').css('height', $('.owl-fotos .owl-wrapper-outer').css('height'));
    QPVerticalGallery.init($('.swiper-container'));
}

var QPEasyZoom = (function() {

    var _init = function(el) {
        _resize(el);
        $(window).resize(function() {
            _resize(el)
        });
    }

    var _resize = function(el) {
        if ($(window).width() >= 992) {
            el.each(function(i, v){
                $(this).easyZoom();
            })
        } else {
            el.each(function(i, v){
                apiEazyZoom = $(this).easyZoom().data('easyZoom').teardown();
            })
        }
    }

    return {
        init: _init,
    }

})();

/**
 * QPGalleryManager
 * @type {{currentIndex, setCurrentIndex}}
 */
var QPGalleryManager = (function() {

    var _collGalleries = {};

    var _updateIndexInPlugins = function(gallery) {

        index = gallery.currentIndex;

        if (QPVerticalGallery.getInstance(gallery.galleryId)) {
            QPVerticalGallery.getInstance(gallery.galleryId).swipeTo(index-1, 300, '');
        }

        QPOwlGallery.goTo(gallery);

    }

    var _addGallery = function(galleryId) {

        if(_collGalleries[galleryId] == undefined) {
            _collGalleries[galleryId] = {
                galleryId: galleryId,
                currentIndex: 1,
            };
        }
    }

    var _getCurrentIndex = function(galleryId) {
        var gallery = _getGalleryById(galleryId);
        if (gallery) {
            return gallery.currentIndex;
        }
        return false;
    }
    var _setCurrentIndex = function(galleryId, index) {
        var gallery = _getGalleryById(galleryId);
        if (gallery) {
            gallery.currentIndex = index;
            _updateIndexInPlugins(gallery);
        }
        return false;
    }

    var _getGalleryById = function(galleryId) {
        if(_collGalleries[galleryId] != undefined) {
            return _collGalleries[galleryId];
        }
        return false;
    }

    return {
        addGallery: _addGallery,
        getCurrentIndex: _getCurrentIndex,
        setCurrentIndex: _setCurrentIndex
    };

})();

/**
 * @type {{init, instance}}
 */
var QPOwlGallery = (function() {

    var _instance = {};

    var _init = function(jqueryContainer, options)
    {
        jqueryContainer.each(function(i, element) {
            $(element).owlCarousel($.extend({
                singleItem : true,
                autoHeight : true,
                lazyLoad : true,
                afterInit: _resizeOwlFotos,
                afterAction: function() {
                    QPGalleryManager.setCurrentIndex($(element).data('gallery-id'), this.owl.currentItem);
                    _resizeOwlFotos();
                }
            }, options));

            _instance[$(element).data('gallery-id')] = {
                element: $(element),
                dataOwl: $(element).data('owlCarousel')
            };
        });

        return _instance;
    }

    var _resizeOwlFotos = function()
    {
        if (_instance.length > 0) {
            if ($(window).width() >= 992) {
                $(_instance).each(function (i, gallery) {
                    gallery.element.find('.owl-controls').hide();
                });
            } else {
                $(_instance).each(function (i, gallery) {
                    gallery.element.find('.owl-controls').show();
                });
            }
        }
    }

    var _getInstance = function(galleryId)
    {
        if (_instance[galleryId] != undefined) {
            return _instance[galleryId];
        }
        return false;
    }

    var _goTo = function(gallery)
    {
        if (_getInstance(gallery.galleryId)) {
            _getInstance(gallery.galleryId).dataOwl.goTo(gallery.currentIndex)
        }
    }

    return  {
        init: _init,
        getInstance: _getInstance,
        goTo: _goTo
    }

})();

/**
 * QPVerticalGallery
 * @type {{init, instance}}
 */
var QPVerticalGallery = (function() {

    var _productThumbnailsSwiper = {};

    var _init = function(jqueryContainer, options) {

        var galleryId = (jqueryContainer).data('gallery-id');
        _productThumbnailsSwiper[galleryId] = jqueryContainer.swiper($.extend({
            mode: 'vertical',
            slidesPerView: 'auto',
            watchActiveIndex: true,
            mousewheelControl: true,
            onSlideClick : function() {
                QPGalleryManager.setCurrentIndex(galleryId, _productThumbnailsSwiper[galleryId].clickedSlideIndex);
            }
        }, options));

        return _productThumbnailsSwiper;
    }

    var _getInstance = function(galleryId) {
        if (_productThumbnailsSwiper[galleryId]) {
            return _productThumbnailsSwiper[galleryId];
        }
        return false;
    }

    return  {
        init: _init,
        getInstance: _getInstance
    }

})();

/**
 * PhotoSwipe
 * @type {{init}}
 */
var QPPhotoSwipe = (function() {

    var _pswp = null;

    var _create = function(jqueryElementGallery, options) {
        var pswpElement = document.querySelectorAll('.pswp')[0];
        var items = _parseThumbnailElements(jqueryElementGallery);
        var defaultOptions = _defaultOptions();
        var options = $.extend(defaultOptions, options);

        _pswp = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
        _pswp.listen('beforeChange', function() {
            QPGalleryManager.setCurrentIndex(_pswp.getCurrentIndex());
        });

        return _pswp;
    }

    var _openGallery = function() {
        _pswp.init();
    };

    var _getInstance = function() {
        return _pswp;
    };

    var _defaultOptions = function() {
        return {
            mainClass: 'pswp--minimal--dark',
            barsSize: {
                top: 0,
                bottom: 0
            },
            captionEl: false,
            fillscreenEl: false,
            shareEl: false,
            bgOpacity: 0.85,
            tapToClose: true,
            tapToToggleControls: false
        }
    };

    var _parseThumbnailElements = function(jqueryElementGallery) {

        var $nodes = jqueryElementGallery.find('a');
        var items = [];

        $nodes.each(function(i, el) {
            childElements = el.children;
            size = el.getAttribute('data-size').split('x');

            item = {
                src: el.getAttribute('href'),
                w: parseInt(size[0], 10),
                h: parseInt(size[1], 10)
            };

            item.o = {
                src: item.src,
                w: item.w,
                h: item.h
            };

            items.push(item);
        });

        return items;
    };

    return {
        create: _create,
        openGallery: _openGallery,
        getInstance: _getInstance
    };

})();