<?php
/* @var $object Marca */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

$form->addElement(new Element\Select("Página:", "data[PAGINA]", SeoPeer::getPaginas(), array(
    "required" => true,
    "id" => "pagina",
    "value" => $object->getPagina(),
)));

$form->addElement(new Element\HTML('<hr style="margin-top: 0;">'));

$form->addElement(new Element\HTML('<div id="registros_response">'));

$form->addElement(new Element\HTML(get_contents(QCOMMERCE_DIR . '/admin/seo/registros.php', array(
    'object' => $object
))));

$form->addElement(new Element\HTML('</div>'));

$form->addElement(new Element\HTML('<div id="metatags_response">'));

$form->addElement(new Element\HTML(get_contents(QCOMMERCE_DIR . '/admin/seo/campos.php', array(
    'object' => $object
))));

$form->addElement(new Element\HTML('</div>'));




$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));


$form->render();
?>
<script>

    $(function() {
        
        var pagina = $('#pagina').val();
        
//        loadRegistros(pagina);

        $(document).on('change', '#pagina', function() {
            loadRegistros($(this).val());
        });
        
        $(document).on('change', '#registros', function() {
            loadMetaTags($('#pagina').val(), $(this).val());
        });

    });

    function loadRegistros(pagina) {
        var url = window.root_path + '/admin/seo/registros.php';
        $('#registros_response').load(url + '?pagina=' + pagina, function() {
            loadMetaTags(pagina);
        });
    }

    function loadMetaTags(pagina, registro_id) {
        
        registro_id = registro_id || '';
        
        var url = window.root_path + '/admin/seo/campos.php';
        $('#metatags_response').load(url + '?pagina=' + pagina + '&registro_id=' + registro_id);
    }
</script>

