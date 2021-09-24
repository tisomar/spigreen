<?php

?>

<div class="container">

    <div class="row">
        <div class="col-xs-12">

        </div>
    </div>

    <div class="row vdivide">
        <div class="col-xs-12 clear-fix">
            <form role="form" id="revendedor">
                <div class="col-xs-12 col-sm-4">
                    <div class="form-group">
                        <label for="text-name">Nome, e-mail, CPF, Chave ou Nome do hotsite:</label>
                        <input class="form-control " type="text" id="text-name" name="text-name" value="">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12 col-sm-4">
                        <label for="address-uf">Estado:</label>
                        <?php include QCOMMERCE_DIR . '/ajax/ajax-estados.php'; ?>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <label for="register-city">Cidade:</label>
                        <div id="response-cidade">
                            <?php include QCOMMERCE_DIR . '/ajax/ajax-cidades.php'; ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div id="revendedores-list">

            </div>
        </div>
    </div>

</div>

<script type="text/javascript">

    $(document).ready(function() {

        getAllResellerWithFilters(true);

        $('body').on('keyup', 'form#revendedor #text-name', function (e) {
            getAllResellerWithFilters();
        });

        $('body').on('change', 'form#revendedor select', function (e) {
            getAllResellerWithFilters();

        });

        $('body').on('submit', 'form#revendedor', function (e) {
            e.preventDefault();
            return false;
        });

        $('body').on('click', 'button.rev-selected', function (e) {

            var revId = $(this).attr('id').replace('rev-', '');
            var revName = $(this).data('name');

            var options = {
                title: 'Confirmação Distribuidor',
                text: 'Confirma '+revName+ ' como seu Distribuidor?' ,
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Sim",
                cancelButtonText: "Não",
                closeOnConfirm: false,
                closeOnCancel: false
            };

            swal(options, function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: window.root_path + "/ajax/confirmRevendedor.php",
                        type: 'POST',
                        data: {id:revId},
                        dataType: 'json',
                        success: function(data){
                            var returned = jQuery.parseJSON(JSON.stringify(data));

                            if(returned.retorno  == 'success'){

                                swal({
                                        title: '',
                                        text: returned.msg,
                                        type: "success",
                                    },
                                    function(){
                                        window.location.href = window.root_path;
                                });

                            } else {
                                swal(returned.title, returned.msg, "error");
                                return false;
                            }

                        }
                    });

                } else {
                    console.log('Aqui');
                    swal("Confirmação Negada", "Escolha outro Distribuidor", "error");
                }
            });
        });

    });


    function getAllResellerWithFilters(filtered){
        var $d = $('#text-name');
        var $estado = $('select#address-uf');
        var $cidade = $('select#register-city');

        var values = [];

        values['estado'] = '';
        values['cidade'] = '';
        values['text'] = '';
        values['filtered'] = filtered == true ? 'true' : '';

        if ($d.length != 0) {
            if ($d.val().length != 0 ) {
                values['text'] = $d.val();
            }
        }

        if ($estado.length != 0) {
            if ($estado.val().length != 0 ) {
                values['estado'] = $estado.val();
            }
        }

        if ($cidade.length != 0) {
            if ($cidade.val().length != 0 ) {
                values['cidade'] = $cidade.val();
            }
        }

        $.ajax({
            url: window.root_path + "/ajax/getRevendedor.php",
            type: 'POST',
            data: {text:values['text'], cidade:values['cidade'], estado:values['estado'], filtered:values['filtered']},
            dataType: 'json',
            success: function(data){

                var returned = jQuery.parseJSON(JSON.stringify(data));
                if(returned.retorno == 'success') {
                    $('#revendedores-list').html(returned.html);
                } else {
                    $('#revendedores-list').html('');
                    alert(returned.msg);
                }

            }
        });

    }


</script>
