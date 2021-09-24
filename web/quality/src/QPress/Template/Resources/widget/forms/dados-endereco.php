<h2>Endereço de entrega</h2>

<input type="hidden" name="csrf_token" value="<?php echo \QPress\CSRF\NoCSRF::generate( 'csrf_token' ); ?>">

<div class="row">
    <div class="col-xs-12">
        <label for="register-cep">* CEP:</label>
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control mask-cep validity-cep" id="register-cep" name="e[CEP]" required
                           pattern="<?php echo REG_CEP; ?>"
                           value="<?php echo $objEndereco->getCep() ?>"
                    >
                </div>
            </div>
            <?php
            $xs = 6;
            $xs = isset($principal) ? 3 : 6;
            ?>
            <div class="col-xs-6 col-sm-<?php echo $xs ?>">
                <a href="http://www.buscacep.correios.com.br" target="_blank" class="btn btn-link">Não sei meu CEP</a>
            </div>
            <?php if($xs == 3):
                $checked = $objEndereco->getEnderecoPrincipal() ? 'checked="checked"' : '';
                ?>
                <div class="col-xs-6 col-sm-<?php echo $xs ?>">
                    <div class="form-group">
                        <label for="address-principal">Seu endereço principal?</label>
                        <input type="checkbox" class="checkbox" id="address-principal" <?php echo $checked ?> name="e[ENDERECO_PRINCIPAL]" >
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-8">
        <div class="form-group">
            <label for="register-street">* Rua:</label>
            <input type="text" class="form-control nome" id="register-street" name="e[LOGRADOURO]" value="<?php echo $objEndereco->getLogradouro() ?>" required>
        </div>
    </div>
    <div class="col-xs-12 col-sm-4">
        <div class="form-group">
            <label for="register-number">* Número:</label>
            <input type="number" class="form-control validity-number" id="register-number" name="e[NUMERO]"  value="<?php echo $objEndereco->getNumero() ?>" required>
        </div>
    </div>
    <div class="col-xs-12">
        <div class="form-group">
            <label for="register-complement nome">Complemento:</label>
            <input class="form-control" type="text" id="register-complement" name="e[COMPLEMENTO]" value="<?php echo $objEndereco->getComplemento() ?>">
        </div>
    </div>
    <div class="col-xs-12 col-sm-4">
        <div class="form-group">
            <label for="register-district">* Bairro:</label>
            <input type="text" class="form-control validity-neighborhood nome" id="register-district" name="e[BAIRRO]" value="<?php echo $objEndereco->getBairro() ?>" required>
        </div>
    </div>
    <div class="col-xs-12 col-sm-4">
        <div class="form-group">
            <label for="address-uf">* Estado:</label>
            <?php include QCOMMERCE_DIR . '/ajax/ajax-estados.php'; ?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-4">
        <div class="form-group">
            <label for="register-city">* Cidade:</label>
            <div id="response-cidade">
                <?php include QCOMMERCE_DIR . '/ajax/ajax-cidades.php'; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Preenchimento form
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
    
    $('#register-street').on('keypress', function(evt){
    var regex = new RegExp("^[ a-zA-Z\b]+$");
    var _this = this
    setTimeout(function() {
        var texto = $(_this).val();
        if (!regex.test(texto))
            { 
                $(_this).val('')
                alert('Não é permitido caracteres especiais.')
            }
        }, 100);
    });

    // FUNÇÂO REGES TRATANDO OS CAMPOS DE CADASTRO DO ENDEREÇO
    function RegTratamento(valorString) {
        let StringRetorno = '';
        StringRetorno = valorString.toLowerCase().replace(/(?<= )[^\s]|^./g, a=>a.toUpperCase()).replace(/\ \ +/g, ' ').replace(/([\*\+\/\(\)\_\$\¨\=\$\#\@\%\&\!\;\>\<\?\|])/g, '');
        return(StringRetorno);
    }

    // FAZENDO TREATAMENTO DOS VALORES INFORMADOS NO FORMULÁRIO
    let formCadastroEndereco = $('#form-cadastro-endereco input');
    formCadastroEndereco.keyup(function() {
        if($(this).attr('id') === 'address-identification' || $(this).attr('id') === 'address-name') {
            $(this).val($(this).val().replace(/\d/g, '')); 
            $(this).val($(this).val().replace('-', ''));
            $(this).val(RegTratamento($(this).val()));
        }else if($(this).attr('id') == 'register-street') {
            $(this).val($(this).val().replace('--', '-'));
            $(this).val(RegTratamento($(this).val()));
        }else if($(this).attr('id') == 'register-complement' || $(this).attr('id') == 'register-district') {
            $(this).val($(this).val().replace('-', ''));
            $(this).val(RegTratamento($(this).val()));
        }
    })

    
    
</script>

<?php
/**
 * Caso os dados venham da página de pre-cadastro,
 * chamar o evento que carrega os dados de endereço.
 */
if (isset($_POST['precadastro'])) {
    echo '<script>'
            . '$(function() {'
                . 'setTimeout(function(){ $("#register-cep").trigger("change") }, 200);'
            . '});'
        . '</script>';
}