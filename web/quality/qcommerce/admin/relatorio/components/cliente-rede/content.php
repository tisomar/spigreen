<?php
use PFBC\Element;

$clienteRoot = ClienteQuery::create()->findRoot();

?>
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
<div class="row">
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Gerações</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive no-label">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table">
                        <thead>
                        </thead>
                        <tbody>
                        <tr>
                            <td style=" width: 90%; background-color: #f9f9f9;">
                                <div class="selectparent">
                                    <label for="cliente-id">Selecione o cliente para transferir:</label>
                                    <input class="form-control" id="cliente-id" name="transferencia[CLIENTE_DESTINATARIO_ID]" />
                                </div>
                            </td>
                            <td class="text-left" style=" width: 10%; background-color: #f9f9f9; vertical-align: bottom; padding-left: 0;">
                                <button class="btn btn-block btn-action _btn-nome-erase " style="background-color: #cbcbcb;"><i class="icon-trash"></i></button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div id="redes-container">


                </div>
            </div>
        </div>
    </div>
</div>

<script>

    $( document ).ready(function() {

        var parentElement = $(".selectparent");

        $('#cliente-id').select2({
            dropdownParent: parentElement,
            width: '100%',
            clear: true,
            ajax: {
                url:"<?php echo $root_path; ?>/ajax/getAllClientes",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params // search term
                    };
                },
                results: function (data) {
                    return {
                        results: data.items
                    };
                },
            },
            placeholder: 'Selecione o cliente',
            minimumInputLength: 3,
            language: "pt-BR"
        }).change(function() {
            pesquisaRede($(this).val());
        });

        getDependentes(<?php echo $clienteRoot->getId() ?>,
            <?php echo $clienteRoot->getId() ?>, '0');

        $('body').on('click', '.linkRede', function(e){
            getDependentes($(this).data('idlogado'), $(this).data('id'), $(this).data('gen'));
            e.preventDefault();
        });

        function pesquisaRede(clienteId) {
            getDependentes(1, 1, 0, clienteId, function() {
                if(clienteId){
                    $('table.table-rede-sized tr input.cliente-rede-id').each(function() {
                        // var funcao = $(this).data('id') == clienteId ? 'show' : 'hide';
                        // $(this).closest('tr')[funcao]();

                        if ($(this).val() == clienteId) {
                            $(this).closest('tr').show();
                        } else {
                            $(this).closest('tr').hide();
                        }
                    });
                    $('ul.table-rede li input.cliente-rede-id').each(function() {
                        if ($(this).val() == clienteId) {
                            $(this).closest('ul').show();
                        } else {
                            $(this).closest('ul').hide();
                        }
                    });
                } else {
                    $("table.table-rede-sized td.nome_cliente_filter").each(function( index ) {
                        $(this).closest('tr').show();
                    });
                    $("ul.table-rede li.nome_cliente_filter").each(function( index ) {
                        $(this).closest('ul').show();
                    });
                }
            });
        }

        // $('body').on('keyup', function(event) {
        //     if (event.keyCode === 13) {
        //         pesquisaRede()
        //     }
        // })

        // $('body').on('click', '._btn-nome', function(e){
        //     pesquisaRede()
        // });

        $('body').on('click', '._btn-nome-erase', function(e){
            $("input._nome_cliente").val("");

            $("table.table-rede-sized td.nome_cliente_filter").each(function( index ) {
                $(this).closest('tr').show();
            });
            $("ul.table-rede li.nome_cliente_filter").each(function( index ) {
                $(this).closest('ul').show();
            });

            e.preventDefault();
        });

        $('body').on('click', 'a.open-modal2', function(e){

            e.preventDefault();
            var url = $(this).attr('href');
            $("#modal-iframe-pedidos .modal-body").html('<iframe width="100%" height="100%" frameborder="0" scrolling="yes" allowtransparency="true" src="'+url+'"></iframe>');
            $("#modal-iframe-pedidos").modal({ open: true });

        });

        $('#modal-iframe-pedidos').on('show.bs.modal', function () {

            $(this).find('.modal-dialog').css({
                width:'100%',
                height:'100%',
                'padding':'0'
            });
            $(this).find('.modal-content').css({
                height:'100%',
                'border-radius':'0',
                'padding':'0'
            });
            $(this).find('.modal-body').css({
                width:'auto',
                height:'100%',
                'padding':'0'
            });
        })

        function getDependentes(idClienteLogado, idClienteRede, geracao, searchRede, callback)
        {
            $.ajax({
                url: "<?php echo $root_path; ?>/ajax/getClientesGeracaoAdm",
                data: { 'idClienteLogado' : idClienteLogado, 'idClienteRede': idClienteRede, 'geracao': geracao, 'searchRede': searchRede},
                type: "POST"
            }).done(function(html){
                $('#redes-container').html(html);

                callback && callback();
            })
        }

    });
</script>