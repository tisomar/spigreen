
<?php if (!$objCliente->isNew() && $objCliente->isPessoaJuridica()): ?>

    <h3>Dadaos Pessoa Jurídica</h3>
    <?php if($strIncludesKey != 'minha-conta-dados'): ?>
        <h2>Dados jurídicos</h2>
    <?php endif; ?>

    <div class="form-group">
        <label for="register-company-name">Razão Social:</label>
        <input class="form-control" type="text" disabled value="<?php echo $objCliente->getRazaoSocial() ?>">
    </div>
    <div class="form-group">
        <label for="register-fancy-name">Nome Fantasia:</label>
        <input class="form-control" type="text" disabled value="<?php echo $objCliente->getNomeFantasia() ?>">
    </div>
    <div class="form-group">
        <label for="register-state-registration">Inscrição Estadual:</label>
        <input class="form-control" type="text" disabled value="<?php echo $objCliente->getInscricaoEstadual() ?>">
    </div>
    <div class="form-group">
        <label for="register-cnpj">CNPJ:</label>
        <input class="form-control" type="text" disabled value="<?php echo $objCliente->getCnpj() ?>">
    </div>

<?php else: ?>

    <?php if($strIncludesKey != 'minha-conta-dados'): ?>
        <h2>Dados jurídicos</h2>
    <?php endif; ?>

    <div class="form-group">
        <label for="register-company-name">* Razão Social:</label>
        <input class="form-control validity-default" type="text" id="register-company-name" name="c[RAZAO_SOCIAL]" value="<?php echo $objCliente->getRazaoSocial() ?>">
    </div>
    <div class="form-group">
        <label for="register-fancy-name">* Nome Fantasia:</label>
        <input class="form-control validity-default" type="text" id="register-fancy-name" name="c[NOME_FANTASIA]" value="<?php echo $objCliente->getNomeFantasia() ?>">
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="register-state-registration">* Inscrição Estadual:</label>
                <input class="form-control validity-default" type="text" id="register-state-registration" name="c[INSCRICAO_ESTADUAL]" value="<?php echo $objCliente->getInscricaoEstadual() ?>">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="register-cnpj">* CNPJ:</label>
                <input class="form-control validity-default mask-cnpj" type="text" id="register-cnpj" name="c[CNPJ]" value="<?php echo $objCliente->getCnpj() ?>">
            </div>
        </div>
    </div>
    
<?php endif; ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('#register-cnpj').blur(function () {
            var cnpj = $("input#register-cnpj").val();

            if (cnpj != '') {
                if(!validarCNPJ(cnpj)) {
                    alert("O CNPJ inserido não e válido");
                    $('input#register-cnpj').val("");
                    $('input#register-cnpj').focus();
                }
            }
        });

        function validarCNPJ(cnpj) {
            cnpj = cnpj.replace(/[^\d]+/g,'');

            if (cnpj.length != 14)
                return false;

            // Elimina CNPJs invalidos conhecidos
            if (cnpj == "00000000000000" ||
                cnpj == "11111111111111" ||
                cnpj == "22222222222222" ||
                cnpj == "33333333333333" ||
                cnpj == "44444444444444" ||
                cnpj == "55555555555555" ||
                cnpj == "66666666666666" ||
                cnpj == "77777777777777" ||
                cnpj == "88888888888888" ||
                cnpj == "99999999999999")
                return false;

            // Valida DVs
            tamanho = cnpj.length - 2
            numeros = cnpj.substring(0,tamanho);
            digitos = cnpj.substring(tamanho);
            soma = 0;
            pos = tamanho - 7;
            for (i = tamanho; i >= 1; i--) {
                soma += numeros.charAt(tamanho - i) * pos--;
                if (pos < 2)
                    pos = 9;
            }
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(0))
                return false;

            tamanho = tamanho + 1;
            numeros = cnpj.substring(0,tamanho);
            soma = 0;
            pos = tamanho - 7;
            for (i = tamanho; i >= 1; i--) {
                soma += numeros.charAt(tamanho - i) * pos--;
                if (pos < 2)
                    pos = 9;
            }
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(1))
                return false;

            return true;
        }
    });
</script>
