<script type="text/javascript">

    $( function(){

        $('#estado').change(function(){

            $.ajax({
                url: '<?php echo $root_path;?>/admin/ajax/cidades.ajax.php?uf='+$('#estado').val()
            }).done(function(dados){

                dados = JSON.parse(dados);
                $selectCidades = $('#cidade');

                var html = '';
                html = html + "<option value='0'>Todas</option>";
                for (var i = 0; i < dados.length; i++ ){
                    html = html + "<option value='"+ dados[i].id +"'>"+ dados[i].nome +"</option>";
                }

                $selectCidades.html(html);

            });
        });

        $('#btnExport').click(function(e){
            if ($("#contatos").length > 0){
                $('#form-distribuidores').submit();
            }
            else {
                alert('Para processeguir com a exportação é necessário efetuar a consulta.');
            }

        });

    });

</script>