<?php

$listas = array('Lista Distribuidores', 'Lista Distribuidores 1');

$contatos = array(
    array('nome' => 'Wallace',
        'email' => 'wperini@gmail.com'),
    array('nome' => 'João da Silva',
        'email' => 'joaosilva@gmail.com'));


$contatos = json_encode($contatos);
$listas   = json_encode($listas);

$host = 'https://mail4web.com.br'; //host do mail4web

$funcao = '/api/contatos/importa'; //funcao da API
$apikey = 'x2kupjqly2wx3rp4ouj34l26ukofbsfn4nfo7qjhgav6vw6mps'; //chave de API do usuario

?>
<form id="form" action="<?= "$host$funcao?apikey=$apikey"; ?>" method="post">

    <input type="hidden" name="contatos" id="contatos" value='<?= $contatos; ?>'>
    <input type="hidden" name="listas" id="listas" value='<?= $listas; ?>'>

    <button type="submit" name="enviar">Enviar Contatos SUBMIT</button>
    <button type="button" id="FormSubmit" name="enviar">Enviar Contatos AJAX</button>

</form>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script type="text/javascript">

    $( function(){

        $("#FormSubmit").click(function(e){

            e.preventDefault();

            var formData = new FormData();
            formData.append('contatos', $('#contatos').val());
            formData.append('listas', $('#listas').val());

            var values = $('#form').serialize();

            $.ajax({
                type: "POST",
                //data: formData,
                data: values,
                dataType: 'json', // add this
                url: '<?= 'http://localhost:82/redefacilbrasil-qcommerce/web/distribuidores_novo/m4w/teste'; ?>',
                //url: '<?= "$host$funcao?apikey=$apikey"; ?>',
                success: function(data){
                    alert(data.resultado);
                },
                error: function(object, status, errorThrown) {
                    alert('msg: '+status + ' | ' + object.status + ' | ' + object.readyState + ' | ' + object.statusText + ' | ' + errorThrown);
                }
            });
        });
    })

</script>
