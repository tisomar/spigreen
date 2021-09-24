<?php use QPress\Template\Widget;
$principal = ClientePeer::isAuthenticad();
?>

<div class="form-group">
    <label for="address-identification">Identificação do endereço:</label>
    <input type="text" class="form-control nome" id="address-identification" name="e[IDENTIFICACAO]" value="<?php echo $objEndereco->getIdentificacao(); ?>" placeholder="Ex: Minha Casa, Meu Trabalho, Casa da praia">
</div>

<div class="form-group">
    <label for="address-name">Nome do Destinatário:</label>
    <input type="text" class="form-control nome" id="address-name" name="e[NOME_DESTINATARIO]" value="<?php echo $objEndereco->getNomeDestinatario() ?>">
</div>

<?php Widget::render('forms/dados-endereco', array(
    'objEndereco'   => $objEndereco,
    'principal'     => $principal
)); ?>

<script>
    // Preenchimento do Nome
    $.fn.capitalize = function () {
        // palavras para ser ignoradas
        var wordsToIgnore = ["da", "de", "do", "das", "dos"],
            minLength = 3;

        function getWords(str) {
            return str.match(/\S+\s*/g);
        }
        this.each(function () {
            if(!this.value){
                return 
            }              
            var words = getWords(this.value.toLowerCase());
            $.each(words, function (i, word) {
                // somente continua se a palavra nao estiver na lista de ignorados
                if (wordsToIgnore.indexOf($.trim(word)) == -1 && $.trim(word).length > minLength) {
                    words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1);
                }
            });
            this.value = words.join("");
        });
    };

    // onblur do campo com classe .nome
    $('.nome').on('blur', function () {
        $(this).capitalize();
        }).capitalize();
    
    $('#address-identification').on('keypress', function(evt){
        var regex = new RegExp("^[ a-zA-Z\b]+$");
        var _this = this
        setTimeout(function() {
            var texto = $(_this).val();
            if (!regex.test(texto))
            { 
                $(_this).val('')
                alert('Não é permitido caracteres especiais ou números. Apenas letras.')
            }
        }, 100);
    });

    $('#address-name').on('keypress', function(evt){
        var regex = new RegExp("^[ a-zA-Z\b]+$");
        var _this = this
        setTimeout(function() {
            var texto = $(_this).val();
            if (!regex.test(texto))
            { 
                $(_this).val('')
                alert('Não é permitido caracteres especiais ou números. Apenas letras.')
            }
        }, 100);
    });
</script>