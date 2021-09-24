<?php /* @var $gatewayPagSeguro \QPress\Gateway\Services\PagSeguroTransparente\PagSeguroTransparente */
$isPagseguroTransparenteCardActive = (bool)(Config::get('pagseguro.cartao_credito') && (Config::get('pagseguro.opcao_pagamento') == "transparente"));
?>

<script type="text/javascript">

    var cardActive = "<?php echo $isPagseguroTransparenteCardActive; ?>";

    $(function() {

        var _urlScript  = '<?php echo $gatewayPagSeguro->getUrlJavaScriptLib() ?>';
        var _sessionId  = '<?php echo $gatewayPagSeguro->getSession() ?>';
        var _amount     = <?php echo format_number($carrinho->getValorTotal(), UsuarioPeer::LINGUAGEM_INGLES) ?>;

        $.getScript(_urlScript, function() {

            /**
             *  Funções de apoio
             */
            var updateInstallments = function (brand) {

                $installmentOptions.attr('disabled', true);
                $installmentOptions.html('<option value="">Carregando...</option>');

                if (brand == null) {

                    brandSelected = null;

                    $installmentOptions.html('<option value="">Preencha os dados do cartão</option>');

                } else {

                    brandSelected = brand;

                    var parameters = {

                        amount: _amount,
                        brand: brand,

                        success: function (response) {

                            // Para obter o array de parcelamento use a bandeira como "chave" da lista "installments"
                            var installments = response.installments[brand];

                            var options = '<option value="">Escolha...</option>';
                            for (var i in installments) {

                                var optionItem = installments[i];

                                // montando o label do option
                                var optionLabel = (optionItem.quantity + "x de " + formatMoney(optionItem.installmentAmount) + ' ' + (optionItem.interestFree ? '(sem juros)' : '(com juros)'));
                                var price = Number(optionItem.installmentAmount).toMoney(2, '.', ',');

                                options += ('<option value="' + optionItem.quantity + '" data-price="' + price + '">' + optionLabel + '</option>');

                            }

                            // Atualizando dados do select de parcelamento
                            $installmentOptions.html(options);

                            $installmentOptions.attr('disabled', false);

                        },
                        error: function () {},
                        complete: function () {}
                    };

                    PagSeguroDirectPayment.getInstallments(parameters);
                }
            }

            var showCardBrand = function(cardBin) {

                PagSeguroDirectPayment.getBrand({
                    cardBin: cardBin,
                    success: function(response) {

                        var brand   = response.brand.name;
                        var iconUrl = 'https://stc.pagseguro.uol.com.br/public/img/payment-methods-flags/68x30/' + brand + '.png';

                        var css = {
                            'background-image'  : 'url(' + iconUrl + ')',
                            'width'             : '68px',
                            'height'            : '30px'
                        }

                        $('.icon-').css(css);

                        if (brandSelected != brand) {
                            updateInstallments(brand);
                        }

                    },
                    error: function(response) {
                    },
                    complete: function(response) {
                    }
                });

            }

            var hideCardBrand = function() {
                // Se não digitou o número do cartão, esconder parcelamento
                $('.icon-').css({
                    'background': 'none'
                });

                updateInstallments(null);
            }


            var verifyBrand = function() {

                // Obtendo apenas os 6 primeiros dígitos (bin)
                var cardBin = $creditCardField.val().substring(0, 6);

                // Atualizar Brand apenas se tiver 6 ou mais dígitos preenchidos
                if (String(cardBin).length >= 6) {
                    // Atualizar Brand
                    showCardBrand(cardBin);
                } else {
                    hideCardBrand();
                }

            }

            /**
             *  Inicia os eventos
             */

            // Identifica a sessão
            PagSeguroDirectPayment.setSessionId(_sessionId);

            /**
             * Consulta os meios de pagamentos disponíveis
             * Obs.: Atualmente utilizado apenas para gerar os bancos para o débito online
             */

            PagSeguroDirectPayment.getPaymentMethods({

                success: function(response) {

                    if (response.error == false) {

                        var baseSrcIcon = 'https://stc.pagseguro.uol.com.br/';
                        var debitoOnline = response.paymentMethods.ONLINE_DEBIT;
                        var baseListTemplate = $("#list-option-template").html();
                        var $listOptions = $("ul#bank-options");

                        $.each(
                            debitoOnline.options,
                            function (bank, options) {
                                if (options.status == "AVAILABLE") {
                                    var src = baseSrcIcon + options.images.MEDIUM.path;
                                    $listOptions.append(baseListTemplate.qpTemplate(bank, src, options.displayName));
                                }
                            }
                        );

                        $listOptions.on('click', 'li', function() {
                            $(this).find(':radio').attr('checked', true);
                        });
                    }
                },

                error: function(response) {},
                complete: function(response) {}
            });

            // Define a bandeira do cartão de crédito
            var brandSelected           = null;

            // Define o campo do número do cartão
            var $creditCardField        = $('.input-credit-card-pagseguro');

            // Define o campo com os parcelamentos
            var $installmentOptions     = $('#payment-options');

            // Verifica bandeira após qualquer mudança nos inputs de cartão de crédito
            $creditCardField

                .on('keypress', function(e) {
                    var verified = (e.which == 8 || e.which == undefined || e.which == 0) ? null : String.fromCharCode(e.which).match(/[^0-9]/);
                    if (verified) {
                        e.preventDefault();
                    }
                })

                .on('keyup change', function() {
                    verifyBrand();
                });


            var $form = $('.form-payment');

            $form.on('submit', function(e) {

                $this = $(this);

                var senderHash = PagSeguroDirectPayment.getSenderHash();

                // Gera o sender_hash
                $('<input>').attr({
                    type    : 'hidden',
                    name    : 'pagseguro[sender_hash]',
                    value   : senderHash
                }).appendTo($this);

                // Identifica o gateway
                $('<input>').attr({
                    type    : 'hidden',
                    name    : 'gateway',
                    value   : 'pagseguro_transparente'
                }).appendTo($this);

                if(cardActive == 1){
                    var isCreditCardOption = $this.attr('id') == 'form-cartao';
                } else {
                    var isCreditCardOption = false;
                }

                if (isCreditCardOption) {

                    var cardNumber = $("#number-card").val();
                    var cvv = $("#security-code").val();
                    var expirationMonth = $("#expiration-month").val();
                    var expirationYear = $("#expiration-year").val();

                    // Cria o token com os valores do cartão informados
                    PagSeguroDirectPayment.createCardToken({

                        cardNumber: cardNumber,
                        cvv: cvv,
                        expirationMonth: expirationMonth,
                        expirationYear: expirationYear,

                        success: function(response) {

                            // Cria o campo com o card_token
                            $('<input>').attr({
                                type    : 'hidden',
                                name    : 'pagseguro[card_token]',
                                value   : response.card.token,
                                id      : 'pagseguro_card_token'
                            }).appendTo($this);

                            // Cria o campo com o valor da parcela para conferência
                            $('<input>').attr({
                                type    : 'hidden',
                                name    : 'pagseguro[installment_value]',
                                value   : $('#payment-options option:selected').data('price')
                            }).appendTo($this);

                            $this.get(0).submit();

                        },
                        error: function(response) {},
                        complete: function() {}
                    });

                    return false;

                }

                return true;

            });

        });
    });

    function formatMoney(valor) {
        return 'R$ ' + Number(valor).toMoney(2,',','.');
    };

    Number.prototype.toMoney = function(decimals, decimal_sep, thousands_sep) {
        var n = this,
            c = isNaN(decimals) ? 2 : Math.abs(decimals),
            d = decimal_sep || '.',
            t = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            sign = (n < 0) ? '-' : '',
            i = parseInt(n = Math.abs(n).toFixed(c)) + '',
            j = ((j = i.length) > 3) ? j % 3 : 0;
        return sign + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : '');
    };

    String.prototype.qpTemplate = function() {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function(match, number) {
            return typeof args[number] != 'undefined'
                ? args[number]
                : match
                ;
        });
    };

</script>

<script type="text/template" id="list-option-template">
    <li class='list-group-item'>
        <input type='radio' name='pagseguro[debito_online]' value='{0}' required>
        <img src='{1}'/>
        {2}
    </li>
</script>