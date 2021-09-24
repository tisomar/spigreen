<script src="<?php echo $root_path?>/distribuidor_scripts/assets/js/libs/AnimatedBorderMenus/js/classie.js"></script>

<?php

switch (strtolower(QPTranslator::getLocale())) {
    case 'en':
        $flag = 'uk';
        break;
    case 'de':
        $flag = 'de';
        break;
    case 'fr':
        $flag = 'fr';
        break;
    case 'es':
        $flag = 'es';
        break;
    default:
        $flag = 'pt-BR';
}

?>


<script type="text/javascript">
    
    $('.table-atividades').cardtable();
    $('.table-distribuidores').stacktable();
    $('.table-clientes').stacktable();
    $('.table-cadastro-sms').cardtable();

    $('.btn-filtrar').click(function () {
        $('#slideout').toggleClass('on');
    });

    $('.btn-close').click(function (e) {
        e.preventDefault();
        $('#slideout').removeClass('on');
    });

    $('#all-select').click(function (event) {
        var $that = $(this);
        $(':checkbox').each(function () {
            this.checked = $that.is(':checked');
            if ($that.is(':checked')) {
                $('.checkbox-replace').addClass('checked');
            } else {
                $('.checkbox-replace').removeClass('checked');
            }
        });
    });


    (function () {
        var ul = $("#navs"), li = $("#navs li"), i = li.length, n = i, r = 120;
        ul.click(function () {
            $(this).toggleClass('active');
            if ($(this).hasClass('active')) {
                for (var a = 1; a < i; a++) {
                    li.eq(a).css({
                        'transition-delay': "" + (5 * a) + "ms",
                        '-webkit-transition-delay': "" + (5 * a) + "ms",
                        'top': (-r * Math.sin(75 / n * a * (Math.PI / 180))),

                    });
                }
            } else {
                li.removeAttr('style');
            }
        });
    })($);
    var tempo = new Number();
    // Tempo em segundos
    tempo = 300;

    function startCountdown() {
        // Se o tempo não for zerado
        if ((tempo - 1) >= 0) {
            // Pega a parte inteira dos minutos
            var min = parseInt(tempo / 60);
            // Calcula os segundos restantes
            var seg = tempo % 60;

            // Formata o número menor que dez, ex: 08, 07, ...
            if (min < 10) {
                min = "0" + min;
                min = min.substr(0, 2);
            }
            if (seg <= 9) {
                seg = "0" + seg;
            }


            horaImprimivel = '00:' + min + ':' + seg;
            $("#cronometro").html(horaImprimivel);
            setTimeout('startCountdown()', 1000);
            tempo--;

            // Quando o contador chegar a zero faz esta ação
        } else {

        }

    }
    jQuery(document).ready(function ($) {
        // Chama a função ao carregar a tela
        startCountdown();
        $('.check-itens').change(function (event) {
            if ($(this).is(':checked')) {
                $('#modal-item-concluido').modal('show', {backdrop: 'static'});
            }
            ;
        });


        $('#chk-1').change(function (event) {
            if ($(this).is(':checked')) {
                jQuery('#modal-item-concluido').modal('show', {backdrop: 'static'});
            }
            ;
        });

    });

    $("#ganho").click(function () {
        $(".fechamento-atividade-ganho").toggle();
    });
    function getRandomInt(min, max)
    {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }
</script>

<!-- JavaScripts initializations and stuff -->
<div></div>
    <script src="<?php echo BASE_URL_ASSETS?>/distribuidor_scripts/assets/js/min/footer.js"></script>
<script src="<?php echo $root_path?>/distribuidor_scripts/assets/js/libs/select2/select2_locale_<?php echo $flag; ?>.js"></script>

<script type="text/javascript">
    window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
        d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
    _.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
        $.src="//v2.zopim.com/?3o1mR0wUNb6OOXYwmnbyoWJyzuJk3B6g";z.t=+new Date;$.
            type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
</script>
