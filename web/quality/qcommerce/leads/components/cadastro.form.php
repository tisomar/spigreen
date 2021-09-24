<?php //var_dump(85858858);die;?>
<style>
    div>label{
        color: white;
        font-weight: bold;
    }
    .btn-modal-leads {
        width: 100px;
        background-color: #00b300;
    }
    @media (min-width: 768px){
        .modal-content {
            box-shadow: 0 5px 15px rgba(0,0,0,.5);
        }
    }

</style>
<?php $pf = true ?>
<div class="padding-form-leads">

<form action="" method="POST" id="form-cadastro-clientes" onsubmit="return validateFormLeads();" class="form-horizontal row-border padding-form-leads">
        <div class="form-group">
            <small><?php echo escape(_trans('leads.texto_pre_formulario')); ?></small>
        </div>
        <div class="form-group">
            <label for="email" class="cols-sm-2 control-label" style=""><?php echo escape(_trans('leads.email_distribuidor')); ?></label>
            <small><?php echo _trans('leads.opcional') ?></small>
            <div class="cols-sm-10">
                <div class="input-group" style="display: block">
                    <input type="email" class="form-control mask " id="email"
                           name="cliente_distribuidor[EMAIL]"
                           placeholder="<?php echo escape(_trans('leads.email_distribuidor')); ?>"
                           value="<?php echo isset($arrClienteDistribuidor['EMAIL']) ? escape($arrClienteDistribuidor['EMAIL']) : '';?>"/>
                </div>
            </div>
        </div>

        <div class="form-group input-lead">
            <label for="nome_razao_social" class="cols-sm-2 control-label">
                <?php echo $pf ? escape(_trans('leads.nome_distribuidor')) : escape(_trans('leads.rs_distribuidor')) ?></label>
            <div class="cols-sm-10">
                <div class="input-group" style="display: block">
                    <input type="text" class="form-control mask " id="nome_razao_social"
                           name="cliente_distribuidor[NOME_RAZAO_SOCIAL]"
                           placeholder="<?php echo $pf ? escape(_trans('leads.nome_distribuidor')) :
                               escape(_trans('leads.rs_distribuidor')) ?>" required
                           value="<?php echo isset($arrClienteDistribuidor['NOME_RAZAO_SOCIAL']) ? escape($arrClienteDistribuidor['NOME_RAZAO_SOCIAL']) : '' ?>"/>
                </div>
            </div>
        </div>

        <div class="form-group input-lead">
            <label for="sobrenome_nome_fantasia" class="cols-sm-2 control-label">
                <?php echo $pf ? escape(_trans('leads.sobrenome_distribuidor')) : escape(_trans('leads.fantasia_distribuidor')) ?></label>
            <div class="cols-sm-10">
                <div class="input-group" style="display: block">
                    <input type="text" class="form-control mask " id="sobrenome_nome_fantasia"
                           name="cliente_distribuidor[SOBRENOME_NOME_FANTASIA]"
                           placeholder="<?php echo $pf ? escape(_trans('leads.sobrenome_distribuidor'))
                               : escape(_trans('leads.fantasia_distribuidor')) ?>" required
                           value="<?php echo isset($arrClienteDistribuidor['SOBRENOME_NOME_FANTASIA']) ? escape($arrClienteDistribuidor['SOBRENOME_NOME_FANTASIA']) : '' ?>"/>
                </div>
            </div>
        </div>

        <?php $telefoneMask = '(99) 99999999[9]'; ?>

        <div class="form-group input-lead">
            <label for="telefone_celular" class="cols-sm-2 control-label"><?php echo escape(_trans('leads.celular_distribuidor')); ?></label>
            <div class="cols-sm-10">
                <div class="input-group" style="display: block">
                    <input type="tel" class="form-control mask " id="telefone_celular"
                           name="cliente_distribuidor[TELEFONE_CELULAR]"
                           placeholder="<?php echo escape(_trans('leads.celular_distribuidor')); ?>"
                           data-inputmask="'mask':'<?php echo $telefoneMask ?>'" required
                           value="<?php echo isset($arrClienteDistribuidor['TELEFONE_CELULAR']) ? escape($arrClienteDistribuidor['TELEFONE_CELULAR']) : '' ?>"/>
                </div>
            </div>
        </div>

        <?php $cepMask = '99999-999'; ?>

        <div class="form-group input-lead">
            <label for="cep" class="cols-sm-2 control-label"><?php echo escape(_trans('leads.cep_distribuidor')); ?></label>
            <div class="cols-sm-10">
                <div class="input-group" style="display: block">
                    <input type="text" class="form-control mask " id="cep"
                           name="cliente_distribuidor[CEP]"
                           placeholder="<?php echo escape(_trans('leads.cep_distribuidor')); ?>"
                           data-inputmask="'mask':'<?php echo $cepMask ?>'"
                           value="<?php echo isset($arrClienteDistribuidor['CEP']) ? escape($arrClienteDistribuidor['CEP']) : '' ?>"/>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="">
                <div class="">
                    <button class="btn-success btn-block btn" id="confirm-lead" style="background-color: #3e903e;" type="submit"><?php echo escape(_trans('leads.btn_salvar_distribuidor')); ?></button>

                </div>
            </div>
        </div>
        <div class="form-group">
            <small><?php echo escape(_trans('leads.texto_pos_formulario')); ?></small>
        </div>
</form>
</div>
<?php
//#373781
?>

<div class="modal fade bd-example-modal-lg success-popup" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="text-center">
                <div style="background-color: #373781;padding: 15px;">
                    <span class="fa fa-check-circle fa-4x" style="color:green"></span> <br>
                    <span class="lead" style="font-weight: 500">Enviado com sucesso!</span><br>
                    <span>Seus dados foram encaminhados para o distribuidor mais próximo do seu endereço, em breve ele fará contato! Obrigado!</span>

                </div>
                <div style="padding: 15px;">
                    <button type="button" class="btn btn-modal-leads" data-dismiss="modal">Ok</button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#confirm-lead').attr('disabled', false);
    });

    function validateFormLeads()
    {
        // Validate URL

        var err = true;
        var email = $("#email").val();
        var nome = $("#nome_razao_social").val();
        var sobrenome = $("#sobrenome_nome_fantasia").val();
        //var cep = $("#cep").val();
        var cel ular = $("#telefone_celular").val();

        var emailReg = /\S+@\S+\.\S+/;

        var cep = $("#cep").val().replace(/\D/g, '');

        if (email === '' || emailReg.test(email)) {}
        else {
            alert("E-mail Inválido");
            err = false;
        }

        if (nome != null || nome != "") {
        } else {
            alert("Nome Inválido");
            err = false;
        }

        if (sobrenome != null || sobrenome != "") {
        } else {
            alert("Sobrenome Inválido");
            err = false;
        }

        var validacep = /^[0-9]{8}$/;

        //Valida o formato do CEP.
        if(validacep.test(cep)) {

        } else {
            alert("Cep Inválido");
            err = false;
        }

        if(telefone_validation(celular)){
        }else{
            alert("Celular Inválido");
            err = false;
        }

        if(err == true){
            return true;
        } else {
            return false;
        }
    }

    function telefone_validation(telefone) {
        //retira todos os caracteres menos os numeros
        telefone = telefone.replace(/\D/g, '');

        //verifica se tem a qtde de numero correto
        if (!(telefone.length >= 10 && telefone.length <= 11)) return false;

        //Se tiver 11 caracteres, verificar se começa com 9 o celular
        if (telefone.length == 11 && parseInt(telefone.substring(2, 3)) != 9) return false;

        //verifica se não é nenhum numero digitado errado (propositalmente)
        /*for (var n = 0; n < 10; n++) {
            //um for de 0 a 9.
            //estou utilizando o metodo Array(q+1).join(n) onde "q" é a quantidade e n é o
            //caractere a ser repetido
            if (telefone == new Array(11).join(n) || telefone == new Array(12).join(n)) return false;
        }*/
        //DDDs validos
        /*var codigosDDD = [
            11, 12, 13, 14, 15, 16, 17, 18, 19,
            21, 22, 24, 27, 28, 31, 32, 33, 34,
            35, 37, 38, 41, 42, 43, 44, 45, 46,
            47, 48, 49, 51, 53, 54, 55, 61, 62,
            64, 63, 65, 66, 67, 68, 69, 71, 73,
            74, 75, 77, 79, 81, 82, 83, 84, 85,
            86, 87, 88, 89, 91, 92, 93, 94, 95,
            96, 97, 98, 99];
        //verifica se o DDD é valido (sim, da pra verificar rsrsrs)
        if (codigosDDD.indexOf(parseInt(telefone.substring(0, 2))) == -1) return false;*/

        return true
    }
</script>

</script>

