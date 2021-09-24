<?php

$tipo = Config::get('precadastro.tipo');
$ativo = Config::get('precadastro.ativo');
$dataFinal = Config::get('precadastro.data_final');
$dias = Config::get('precadastro.dias_corridos');

$date = new DateTime($dataFinal)


?>
<div class="table-responsive">
    <form method="post" action="">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
            <tbody>
                <tr>
                    <td>Pré Cadastro ativo?</td>
                    <td ><select id="ativo"  name="data[precadastro.ativo]" class="form-control">
                            <option value="1" <?php echo $ativo == '1' ? 'selected="selected"' : ''; ?>>Sim</option>
                            <option value="0" <?php echo $ativo == '0' ? 'selected="selected"' : ''; ?>>Não</option>
                        </select>
                    </td>

                </tr>
                <tr class="optiones">
                    <td> Tipo de Pré Cadastro:</td>
                    <td ><select id="tipo" name="data[precadastro.tipo]" class="form-control">
                            <option value="data" <?php echo $tipo == 'data' ? 'selected="selected"' : ''; ?>>Data Fixa</option>
                            <option value="dias" <?php echo $tipo == 'dias' ? 'selected="selected"' : ''; ?>>Dias Corridos</option>
                        </select></td>

                </tr>
                <tr class="optiones" id="dias">
                    <td data-title="Nome">Quantidade de Dias Corridos (valor inteiro):</td>
                    <td data-title="Pontos"><input class="form-control"
                                                   value="<?php echo $dias; ?>"
                                                   name="data[precadastro.dias_corridos]" type="number"
                                                   min="1"
                                                   step="1"
                                                   onkeypress='return event.charCode >= 48 && event.charCode <= 57' placeholder="Número de Dias"></td>

                </tr>
                <tr class="optiones" id="data">
                    <td data-title="Nome">Data para finalização do pré cadastro</td>
                    <td data-title="Pontos"><input class="form-control _datepicker mask-date"
                                                   id="datepicker"
                                                   name="data[precadastro.data_final]"
                                                   type="text"
                                                   value="<?php echo $date->format('d/m/Y'); ?>"></td>

                </tr>

            </tbody>
        </table>
        <div class="text-center">
            <input class="btn btn-success" type="submit" value="Salvar">
        </div>
    </form>
</div>
<script type="text/javascript">
    $(function() {

        $('#datepicker').datepicker({
            minDate: 0

        });

        function validateTipo(value) {
            if(value == 'data'){

                activeTipo('data');
                desactiveTipo('dias');
            } else if(value == 'dias'){

                activeTipo('dias');
                desactiveTipo('data');
            }
        }

        function activeTipo(valAc) {

            $('#'+valAc).show()
        }

        function desactiveTipo(valDs) {
            $('#'+valDs).hide();
        }

        validateTipo('<?php echo $tipo; ?>');

        $('#tipo').on('change', function (e) {
            validateTipo($(this).val());
        })


        function activeOptiones() {
            $('.optiones').show();
            validateTipo('<?php echo $tipo; ?>');
        }

        function desactiveOptiones() {
            $('.optiones').hide();
        }

        function validateOptiones(value) {
            if(value == '1'){
                activeOptiones();
            } else if(value == '0'){

                desactiveOptiones()
            }
        }

        validateOptiones('<?php echo $ativo; ?>');

        $('#ativo').on('change', function (e) {
            validateOptiones($(this).val());
        })

    });
</script>
