<script type="text/javascript">

    $( function(){

       $('#btnExport').click(function(){

            if ($("#contatos").length > 0){
                $('#form-distribuidores').submit();
            }
            else {
                alert('Para processeguir com a exportação é necessário efetuar a consulta.');
            }

        });
    })

</script>