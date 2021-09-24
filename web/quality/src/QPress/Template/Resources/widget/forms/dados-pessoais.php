<?php
$isMinhaConta = $strIncludesKey == 'minha-conta-dados';
use QPress\Template\Widget;


$objEndereco = new Endereco();
$ativacaoPatrocinador = Config::get('clientes.ativacao_patrocinador');

$preActive = false;

if (Config::get('precadastro.ativo')) :
    if (Config::get('precadastro.tipo') == 'data') :

        $preData = Config::get('precadastro.data_final');
        $datePre = new DateTime($preData.' 00:00:00');
        $dateNow = new DateTime(date('Y-m-d').' 00:00:00');

        if ($dateNow->getTimestamp() <= $datePre->getTimestamp()) :
            $preActive = true;
        endif;
    else :
        $preActive = true;
    endif;
endif;

if (ClientePeer::isAuthenticad()) :
    $idClienteLogado = ClientePeer::getClienteLogado()->getId();
else :
    $idClienteLogado = -1;
endif;

$franqueadoCliente  = ClientePeer::getFranqueadoSelecionado($container);

if ($franqueadoCliente === false && $container->getSession()->has('CODIGO_PATROCINADOR')) :

    $codigoPatrocinador = $container->getSession()->get('CODIGO_PATROCINADOR');

    if (ctype_digit($codigoPatrocinador)) :
        $objPatrocinador = ClienteQuery::create()->findOneByChaveIndicacao($codigoPatrocinador);
    else :
        $objPatrocinador = ClienteQuery::create()->findOneByEmail($codigoPatrocinador);
    endif;

    if ($objPatrocinador && $objPatrocinador->isInTree()) :
        $franqueadoCliente = $objPatrocinador;
    endif;
endif;

?>
<input type="hidden" name="csrf_token" value="<?php echo \QPress\CSRF\NoCSRF::generate( 'csrf_token' ); ?>">

<?php if(!$isMinhaConta): ?>
    <h2>Dados pessoais</h2>
<?php endif; ?>

<div class="form-group">
    <label for="register-name">* Nome Completo: </label>
    <input type="text" class="form-control validity-name nome" id="register-name" name="c[NOME]" value="<?php echo $objCliente->getNome() ?>" required>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="register-cpf">* CPF:</label>
            <input type="text" class="form-control validity-cpf mask-cpf" id="register-cpf" <?php if ($isMinhaConta): ?> disabled <?php else: ?> name="c[CPF]" required <?php endif; ?> value="<?php echo $objCliente->getCpf() ?>">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="register-phone">* Telefone:</label>
            <input class="form-control validity-tel mask-tel" type="text" id="register-phone" name="c[TELEFONE]" pattern="<?php echo REG_TEL; ?>" required value="<?php echo $objCliente->getTelefone() ?>">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label <?php echo !$isMinhaConta ? 'for="birth-date"' : ''; ?>>* Data Nascimento:</label>
            <input type="text" <?php if ($isMinhaConta): ?> disabled class="form-control" <?php else: ?> class="form-control validity-birthday mask-date" id="birth-date" name="c[DATA_NASCIMENTO]" pattern="<?php echo REG_DATE; ?>" required <?php endif; ?> value="<?php echo $objCliente->getDataNascimento('d/m/Y') ?>">
        </div>
    </div>
</div>

<div class="form-group">
    <label for="register-email">* E-mail:</label>
    <input
            type="email"
            class="form-control validity-email"
            id="register-email"
            name="c[EMAIL]"
            value="<?= $objCliente->getEmail() ?>"
            placeholder="exemplo@exemplo.com"
            autocomplete="email"
            required
    >
</div>

<?php if ($objCliente->isNew()): ?>
    <div class="form-group">
        <label for="register-email">* Confirmar e-mail:</label>
        <input
                type="email"
                onpaste="return false;"
                ondrop="return false;"
                class="form-control validity-email"
                id="register-email-confirmation"
                name="c[CONFIRMATION_EMAIL]"
                placeholder="exemplo@exemplo.com"
                autocomplete="off"
                required
        >
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="register-pass">* Senha:</label>
                <input class="form-control validity-password" type="password" id="register-pass" name="c[SENHA]" autocomplete="off" pattern="<?php echo REG_PASSWORD; ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="register-confirm-pass">* Confirmação de Senha:</label>
                <input class="form-control validity-password" type="password" id="register-confirm-pass" name="c[SENHA_CONFIRMACAO]" autocomplete="off" pattern="<?php echo REG_PASSWORD; ?>" required>
            </div>
        </div>
    </div>

    <input type="hidden" value="<?php echo $franqueadoCliente instanceof Cliente ? 0 : 1 ?>" name="c[TIPO_CONSUMIDOR]">

<?php endif; ?>
<?php Widget::render('forms/dados-endereco', array(
    'objEndereco' => $objEndereco
)); ?>

<?php if($ativacaoPatrocinador && $objCliente->isNew()):
    if (!$franqueadoCliente) : ?>
        <hr>
        <h2>Você conhece um distribuidor Spigreen?</h2>
        <div class="form-group">
            <div class="radio">
                <label>
                    <input type="radio" name="radio-conhece-patrocinador" id="radio-conhece-patrocinador" checked>
                    Sim, conheço
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="radio">
                <label>
                    <input type="radio" name="radio-conhece-patrocinador" id="radio-nao-conhece-patrocinador">
                    Não, o sistema fará busca
                </label>
            </div>
        </div>
    <?php endif;  ?>

    <div class="form-group" id="patrocinador">
        <label for="register-pre-cadastro">Código ou e-mail do distribuidor:</label>
        <input type="text" class="form-control" id="codigo-patrocinador" name="codigo_patrocinador"
               value="<?php echo $franqueadoCliente instanceof Cliente ? $franqueadoCliente->getChaveIndicacao() : ''; ?>"
            <?php echo $franqueadoCliente instanceof Cliente ? 'readonly' : ''; ?>>
        <div class="text-center" id="patrocinador-dados">

        </div>

        <input type="hidden" name="patrocinador-id" id="patrocinador-id" value="">
        <input type="hidden" name="patrocinador-nome" id="patrocinador-nome" value="">
    </div>

    <?php if (!$franqueadoCliente) : ?>
    <div class="form-group" id="localizar-patrocinador">
        <div class="form-group">
            <label for="cep-localizar-patrocinador">Digite seu CEP para localizar um distribuidor:</label>
            <input type="text" class="form-control mask-cep validity-cep" id="cep-localizar-patrocinador"
                   name="cep-localizar-patrocinador">
        </div>
        <div class="form-group">
            <button id="button-localilzar-patrocinador" class="btn btn-success">Localizar</button>
        </div>
        <div class="form-group" id="patrocinadores-disponiveis">

        </div>
    </div>
    <hr>
<?php endif; ?>
<?php endif; ?>

<input type="hidden" value="<?php echo $idClienteLogado ?>" name="id-cliente-logado">

<?php if($ativacaoPatrocinador && $objCliente->isNew()): ?>
    <script type="text/javascript">
        function setPatrocinador(id) {
            $('#patrocinador-id').val(id);
        }

        $(document).ready(function() {
            $('#codigo-patrocinador').on('blur', function (e) {
                var field = $(this);
                field.attr('disabled', true);
                var patrocinador = $(this).val();
                $.ajax({
                    url: window.root_path + "/ajax/getPatrocinador",
                    type: 'POST',
                    data: 'patrocinador='+patrocinador,
                    success: function(data){
                        var returned = $.parseJSON(data);
                        //alert(returned.msg);
                        if(returned.retorno == 'success'){
                            $('#patrocinador-dados').html(returned.html);
                            $('#patrocinador-id').val(returned.id);
                            $('#patrocinador-nome').val(returned.nome);
                        } else {
                            $('#patrocinador-dados').html('');
                            $('#patrocinador-id').val('');
                            $('#patrocinador-nome').val('');
                            field.val('');
                        }
                    }

                });

                setTimeout(function(){
                    field.removeAttr('disabled');
                }, 3000);
            });

            // Preenchimento do Nome
            $.fn.capitalize = function () {
                // palavras para ser ignoradas
                var wordsToIgnore = ["de", "da", "das", "dos"],
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

            $('#register-cpf').blur(function () {
                var cpf = $("input#register-cpf").val();

                if (cpf != '') {
                    if(!validarCPF(cpf)) {
                        alert("O CPF inserido não e válido");
                        $('input#register-cpf').val("");
                        $('input#register-cpf').focus();
                    }
                }
            });

            $('#register-cpf').blur(function () {
                var cpf = $("input#register-cpf").val();

                if (cpf != '') {
                    if(!validarCPF(cpf)) {
                        alert("O CPF inserido não e válido");
                        $('input#register-cpf').val("");
                        $('input#register-cpf').focus();
                    }
                }
            });

            function validarCPF(cpf) {
                cpf = cpf.replace(/[^\d]+/g, '');
                if (cpf == '') return false;
                // Elimina CPFs invalidos conhecidos
                if (cpf.length != 11 ||
                    cpf == "00000000000" ||
                    cpf == "11111111111" ||
                    cpf == "22222222222" ||
                    cpf == "33333333333" ||
                    cpf == "44444444444" ||
                    cpf == "55555555555" ||
                    cpf == "66666666666" ||
                    cpf == "77777777777" ||
                    cpf == "88888888888" ||
                    cpf == "99999999999")
                    return false;
                // Valida 1o digito
                var add = 0;
                for (i = 0; i < 9; i++)
                    add += parseInt(cpf.charAt(i)) * (10 - i);
                var rev = 11 - (add % 11);
                if (rev == 10 || rev == 11)
                    rev = 0;
                if (rev != parseInt(cpf.charAt(9)))
                    return false;
                // Valida 2o digito
                add = 0;
                for (i = 0; i < 10; i++)
                    add += parseInt(cpf.charAt(i)) * (11 - i);
                rev = 11 - (add % 11);
                if (rev == 10 || rev == 11)
                    rev = 0;
                if (rev != parseInt(cpf.charAt(10)))
                    return false;
                return true;
            }

            // Funções relacionadas ao patrocinador
            function verificaConhecePatrocinador() {
                if($("input:radio[id='radio-conhece-patrocinador']").is(":checked")) {
                    $('#patrocinador').show();
                    $('#localizar-patrocinador').hide();
                    $('#codigo-patrocinador').val("");
                    $('#codigo-patrocinador').trigger("blur");
                    $('#cep-localizar-patrocinador').val("");
                    $('#patrocinadores-disponiveis').empty();
                    setPatrocinador(null);
                }

                if($("input:radio[id='radio-nao-conhece-patrocinador']").is(":checked")) {
                    $('#patrocinador').hide();
                    $('#localizar-patrocinador').hide();
                    $('#codigo-patrocinador').val("03719271797");
                    $('#cep-localizar-patrocinador').val("");

                    $('#codigo-patrocinador').trigger("blur");

                }
            }

            verificaConhecePatrocinador();

            $("input:radio[name='radio-conhece-patrocinador']").on("change", function () {
                verificaConhecePatrocinador();
            });

            $('#button-localilzar-patrocinador').on("click", function (event) {
                event.preventDefault();

                var $this = $(this);
                var cep = $('#cep-localizar-patrocinador').val();

                if (!cep) {
                    alert('Informe o cep!');
                    $this.data('page', 1);
                } else {
                    var page = $this.data('page') || 1;

                    $.ajax({
                        url: window.root_path + '/ajax/consulta-patrocinador-por-cep',
                        data: {
                            cep: cep,
                            page: page,
                        },
                        dataType: 'json',
                        success: function (response) {
                            $('#patrocinadores-disponiveis').empty();
                            $('#patrocinadores-disponiveis').append("<p>Selecione um dos distribuidores abaixo:</p>");

                            var checked = 'checked';

                            $this.data('page', response.final ? 1 : page + 1);

                            response.clientes && response.clientes.forEach(function (cliente) {
                                var span = cliente.nome +
                                    " - " + cliente.codigo_patrocinador;

                                span += cliente.cidade_uf ? " - " + cliente.cidade_uf : '';

                                $('#patrocinadores-disponiveis').append(
                                    "<div class='form-group'>" +
                                    "<div class='radio'>" +
                                    "<label>" +
                                    "<input type='radio' name='patrocinador-disponivel' value='" + cliente.id + "' " + checked + " onclick='setPatrocinador(" + cliente.id + ")'>" +
                                    "<span>" + span + "</span>" +
                                    "</label>" +
                                    "</div>" +
                                    "</div>"
                                );

                                if (checked !== '') {
                                    setPatrocinador(cliente.id);
                                }

                                checked = '';
                            });
                        }
                    });
                }
            });
        });
    </script>
<?php endif; ?>

<?php if($ativacaoPatrocinador && $objCliente->isNew() && $franqueadoCliente instanceof Cliente): ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#codigo-patrocinador').trigger('blur');
        });
    </script>
<?php endif; ?>
