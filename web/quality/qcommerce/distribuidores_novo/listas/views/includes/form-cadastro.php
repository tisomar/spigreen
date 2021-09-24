<div class="col-md-5 col-lg-6">
    <div class="form-group row">
        <div class="col-sm-2 col-md-12 col-lg-3">
            <label class="control-label"><?php echo escape(_trans('agenda.tipo_pessoa')); ?></label>
        </div>
        <div class="col-lg-8 radioTipo">
            <label class="radio-inline radio radio-replace">
                <input type="radio" class="tipo" id="inlineradio1" name="cliente_distribuidor[TIPO]" value="<?php echo escape(ClienteDistribuidor::TIPO_PESSOA_FISICA) ?>" required> <?php echo escape(_trans('agenda.fisica')); ?>
            </label>
            <label class="radio-inline radio radio-replace">
                <input type="radio" class="tipo" id="inlineradio2" name="cliente_distribuidor[TIPO]" value="<?php echo escape(ClienteDistribuidor::TIPO_PESSOA_JURIDICA) ?>" checked required> <?php echo escape(_trans('agenda.juridica')); ?>
            </label>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-2 col-md-12 col-lg-3">
            <label class="control-label" for="email"><?php echo escape(_trans('agenda.email')); ?></label>
        </div>
        <div class="col-lg-8">
            <input type="email" class="form-control" id="email" placeholder="examplo@email.com.br" name="cliente_distribuidor[EMAIL]" value="<?php echo escape($arrClienteDistribuidor['EMAIL']) ?>" required>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-2 col-md-12 col-lg-3">
            <label class="control-label" for="nome_razao_social"><?php echo escape(_trans('agenda.nome')); ?></label>
        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" id="nome_razao_social" placeholder="<?php echo escape(_trans('agenda.nome_completo')); ?>" name="cliente_distribuidor[NOME_RAZAO_SOCIAL]" value="<?php echo escape($arrClienteDistribuidor['NOME_RAZAO_SOCIAL']) ?>" required>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-2 col-md-12 col-lg-3">
            <label class="control-label" for="telefone_celular"><?php echo escape(_trans('agenda.celular')); ?></label>
        </div>
        <div class="col-lg-8">
            <input type="tel" class="form-control" data-mask="phone" id="telefone_celular" name="cliente_distribuidor[TELEFONE_CELULAR]" required>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-2 col-md-12 col-lg-3">
            <label class=" control-label" for="whatsapp"><?php echo escape(_trans('agenda.whatsapp')); ?></label>

        </div>
        <div class="col-lg-8">
            <input type="tel" class="form-control" data-mask="phone"
                   id="whatsapp" name="cliente_distribuidor[WHATSAPP]" >
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-2 col-md-12 col-lg-3">
            <label class=" control-label" for="cep"><?php echo escape(_trans('agenda.cep')); ?></label>

        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" id="cep" name="cliente_distribuidor[CEP]" data-mask="cep" data-inputmask="'mask':'99999-999'" maxlength="9">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-2 col-md-12 col-lg-3">
            <label class=" control-label" for="endereco"><?php echo escape(_trans('agenda.endereco')); ?></label>

        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" id="endereco" name="cliente_distribuidor[ENDERECO]">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-2 col-md-12 col-lg-3">
            <label class=" control-label"><?php echo escape(_trans('agenda.numero')); ?></label>

        </div>
        <div class="col-lg-8">
            <input type="number" class="form-control" id="numero" name="cliente_distribuidor[NUMERO]">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-2 col-md-12 col-lg-3">
            <label class=" control-label"><?php echo escape(_trans('agenda.bairro')); ?></label>

        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" id="bairro" name="cliente_distribuidor[BAIRRO]">
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group row">
        <div class="col-sm-2 col-md-12 col-lg-3">
            <label class=" control-label"><?php echo escape(_trans('agenda.complemento')); ?></label>

        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="cliente_distribuidor[COMPLEMENTO]" >
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-2 col-md-12 col-lg-3">
            <label class=" control-label"><?php echo escape(_trans('agenda.estado')); ?></label>
        </div><?php
        
            $collEstados = EstadoQuery::create()->orderByNome()->find();
        
        ?><div class="col-lg-8">
            <select name="cliente_distribuidor[ESTADO]" id="estados" class="form-control">
                <option></option><?php
                    
                    /* @var $objEstado Estado */
                foreach ($collEstados as $objEstado) {
                    ?><option value="<?php echo $objEstado->getId(); ?>">
                        <?php echo $objEstado->getNome(); ?>
                        </option><?php
                }

                ?></select>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-2 col-md-12 col-lg-3">
            <label class=" control-label"><?php echo escape(_trans('agenda.cidade')); ?></label>

        </div>
        <div class="col-lg-8">
            <select name="cliente_distribuidor[CIDADE]" id="cidades" class="form-control">
                <option></option>
            </select>
        </div>
    </div>  
    <div class="panel panel-primary">
        <div class="panel-heading" >
            <div class="panel-title text-uppercase">
                <?php echo escape(_trans('agenda.dados_complementares')); ?>
            </div>
        </div>
        <div class="panel-body">

            <div class="form-group row">
                <div class="col-sm-2 col-md-12 col-lg-3">
                    <label class="control-label" for="telefone"><?php echo escape(_trans('agenda.telefone_fixo')); ?></label>
                </div>
                <div class="col-lg-8">
                    <input type="tel" data-mask="phone" class="form-control mask" id="telefone" name="cliente_distribuidor[TELEFONE]" data-inputmask="'mask':'(99) 9999-9999'" value="">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-2 col-md-12 col-lg-3">
                    <label id="label_cpf_cnpj" class="control-label" for="cpf_cnpj"><?php echo escape(_trans('agenda.cpf')); ?></label>
                </div>

                <div class="col-lg-8">
                    <input type="text" data-mask="cpf_cnpj" class="form-control mask" id="cpf_cnpj" name="cliente_distribuidor[CPF_CNPJ]" data-inputmask="'mask':'999.999.999-99'" value="">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-2 col-md-12 col-lg-3">
                    <label id="label_rg_ie" class="control-label" for="rg_ie"><?php echo escape(_trans('agenda.rg')); ?></label>
                </div>

                <div class="col-lg-8">
                    <input type="text" class="form-control" id="rg_ie" name="cliente_distribuidor[RG_IE]" value="">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-12 col-md-12 col-lg-3">
                    <label class="control-label" for="data_nascimento_data_fundacao"><?php echo escape(_trans('agenda.data_nascimento')); ?></label>
                </div>
                <div class="col-lg-8">
                    <input type="text" class="form-control datepicker" id="data_nascimento_data_fundacao" name="cliente_distribuidor[DATA_NASCIMENTO_DATA_FUNDACAO]" value="">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-sm-2 col-md-12 col-lg-3">
                    <label class="control-label"><?php echo escape(_trans('agenda.sexo')); ?></label>
                </div>
                <div class="col-lg-8 radioSexo">
                    <label class="radio-inline radio radio-replace">
                        <input type="radio" id="inlineradio3" name="cliente_distribuidor[SEXO]" value="M" checked=""> <?php echo escape(_trans('agenda.masculino')); ?>
                    </label>
                    <label class="radio-inline radio radio-replace">
                        <input type="radio" id="inlineradio4" name="cliente_distribuidor[SEXO]" value="F"> <?php echo escape(_trans('agenda.feminino')); ?>
                    </label>
                </div>
            </div>
        </div>

    </div>
</div>

<script>

    $('select[name="cliente_distribuidor[ESTADO]"]').on('change', function() {

        var estadoId = $(this).val();
        
        $.ajax({
            url: window.root_path +'/ajax/ajax_cidades/',
            type: 'GET',
            data: 'estadoId=' + estadoId,
            success: function(html){
                $('#cidades').html(html);
                $('#cidades').removeAttr('disabled');
            }
        });
        return false;
        
    });

    /**
    * Função responsável por buscar o CEP
    */
   
    $('#cep').on('change', function() {
        
        var cep = $(this).val();
        
        var pattern = /[0-9]{5}-[0-9]{3}/;

        if (pattern.test(cep)) {

            $.ajax({
                //url: "https://qapi.com.br/correios/endereco/" + cep,
                url: window.root_path + '/ajax/busca_cep/' + cep,
                cache: false,
                dataType: 'json',
                success: function(response) {

                    var object = response;
                    if ( (typeof object != 'undefined') && (response !== null) ) {
                        
                        console.log(object);
                        
                        // Logadouro + Endereço
                        $('#endereco').val((object.logradouro ? object.logradouro + ' ' : ''));

                        // Bairro
                        $('#bairro').val(object.bairro);
                        
                        $.ajax({
                            url: window.root_path +'/ajax/ajax_estados/',
                            type: 'GET',
                            data: 'sigla=' + object.uf,
                            success: function(html){
                                $('#estados').html(html);
                                // Cidade
                                $.ajax({
                                    url: window.root_path +'/ajax/ajax_cidades/',
                                    type: 'GET',
                                    data: 'estadoId=' + $("#estados").val() + '&cidade=' + object.cidade,
                                    success: function(html){
                                        $('#cidades').html(html);
                                        $('#numero').focus();
                                    }
                                });
                            }
                        });
                        
                    } else {
                        // Sem retorno do webservice para o CEP informado.
                        alert("Não foi possivel encontrar informações relacionadas ao CEP informado!");
                    }

                },
                error: function(x, t, m) {

                    if (t === "timeout") {
                        alert("Não foi possivel encontrar informações relacionadas ao CEP informado!");
                    }

                }
            });
        } else {
            // CEP com formatação incorreta
            alert("O CEP informado é inválido. Por favor, informe um CEP válido.");
        }

        return true;
        
    });
   
</script>
