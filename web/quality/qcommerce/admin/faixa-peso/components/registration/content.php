<?php

/* @var $object TransportadoraFaixaPeso */
/* @var $container \QPress\Container\Container */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

$form->addElement(new Element\Number("Peso (até <i>n</i> gramas):", "data[PESO]", array(
    "value" => $object->getPeso(),
    "append" => 'gramas',
    "shortDesc" => 'Define o peso inicial para montar as faixas. Ex.: a partir de 1.000 gramas cobrar R$ N,nn',
    "required" => true,
)));

$form->addElement(new Element\Number("Prazo para entrega (em dias):", "data[PRAZO_ENTREGA]", array(
    "value" => $object->getPrazoEntrega(),
    "shortDesc" => 'Define o prazo de entrega. Ex.: em até 7 dias',
    "required" => true,
)));

$form->addElement(new Element\Radio("Tipo de Cobrança:", "data[TIPO]", TransportadoraFaixaPesoPeer::getTipoListAdmin(), array(
    "value" => $object->getTipo(),
    "required" => true,
    "class" => "tipo_cobranca",
    'shortDesc' => ''
)));


$form->addElement(new Element\Textbox("Valor:", "data[VALOR]", array(
    "value" => $object->getValorFormatado(),
    "required" => true,
    "id" => "valor"
)));

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));


$form->render();
?>


<script>
    $(function() {
        $('body').on('change', '.tipo_cobranca', function() {
            if ($(this).val() == '<?php echo TransportadoraFaixaPesoPeer::TIPO_PORCENTAGEM ?>') {
                initMaskPercent('#valor');
            } else {
                initMaskMoney('#valor');
            }
            $('#valor').focus();
        });
        $('.tipo_cobranca:checked').trigger('change');
    })
</script>
