<style>
    body .select2-container--default .select2-selection--single{
        display: block;
        width: 100%;
        height: 48px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #666666;
        background-color: #ffffff;
        background-image: none;
        border: 1px solid #cccccc;
        border-radius: 3px;
        box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.15);
        transition: all 0.15s linear;
    }
    body .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 36px;
    }
    body .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: 46px;
    }
</style>

<form role="form" method="post" class="form-contact form-disabled-on-load">
    <h2>Informe os dados da transferência</h2>
    <div class="form-group selectparent">
        <label for="cliente-id">Selecione o cliente para transferir:</label>
        <select class="form-control" id="cliente-id" name="transferencia[CLIENTE_DESTINATARIO_ID]">
        </select>
    </div>
    <div class="cliente-transferir-confirmado" style="display: none">
        <div class="form-group">
            <label for="qtd-bonus">* Quantidade de Bônus:</label>
            <input type="number" class="form-control" step="0.01" id="qtd-bonus" name="transferencia[QUANTIDADE_PONTOS]" required min="1" max="<?= (int) $pontosDisponiveis ?>" value=""/>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-theme btn-block">Enviar</button>
        </div>
    </div>
</form>

<script>
    $(function() {
        var parentElement = $(".selectparent");

        $('#cliente-id').select2({
            dropdownParent: parentElement,
            width: '100%',
            ajax: {
                url: window.root_path+'/ajax/getClienteTransferencia',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term // search term

                    };
                },
                processResults: function (data) {
                    return {
                        results: data.items
                    };
                },
            },
            placeholder: 'Selecione o cliente',
            minimumInputLength: 1,
            language: "pt-BR"
        });

        $('#cliente-id').on('change',function () {
            var idCliente = $(this).val();
            if(idCliente > 0){
                $('.cliente-transferir-confirmado').show()
            } else {
                $('#qtd-pontos').val('');
                $('.cliente-transferir-confirmado').hide()

            }
        });
    });
</script>