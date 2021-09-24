<script type="text/javascript">

    $( function(){

        $('#lista').change(function(){
            $('#newlista').val('');
        });

        $('#newlista').keyup(function(){
            $('#lista').val('');
        });

        $("#btnSubmit").click(function(e){

            e.preventDefault();

            if ($('#lista').find(":selected").val())
                var lista = new Array($('#lista').find(":selected").val());
            else
                var lista = new Array($('#newlista').val());

            if (lista == ''){
                alert('Selecione uma lista de contatos do Mail for Web ou crie uma nova.');
                return false;
            }

            $("#btnSubmit").attr("disabled",true);
            $("#btnSubmit").html("Aguarde, iniciando exportaçao dos contatos para o Mail For Web...");

            var contatos        = $('#contatos').val();
            var url             = $('#url').val();
            var url_redirect    = $('#url_redirect').val();

            $.ajax({
                type: "POST",
                data: { contatos: contatos, listas: lista },
                dataType: 'json',
                url: window.root_path + '/distribuidores_novo/m4w/export',
                success: function(data) {

                    if (data.resultado != 'erro') {
                        var url = '?id=' + data.id_importacao + '&redirect=' + url_redirect;
                        location.href = window.root_path + '/distribuidores_novo/m4w/integracao.result.php' + url;
                    }
                    else
                        alert("Erro ao efetuar a importação !!!!");
                },
                error: function(object, status, errorThrown) {
                    alert('msg: '+status + ' | ' + object.status + ' | ' + object.readyState + ' | ' + object.statusText + ' | ' + errorThrown);
                }
            });

        });
    })

</script>