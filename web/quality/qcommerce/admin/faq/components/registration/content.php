<?php
/* @var $object Faq */

use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));


$form->addElement(new Element\Textarea("Pergunta:", "data[PERGUNTA]", array(
    "value" => $object->getPergunta(),
    'required' => true,
)));

$form->addElement(new Element\Textarea("Resposta:", "data[RESPOSTA]", array(
    "value" => $object->getResposta(),
//    'class' => 'fullscreen',
    'required' => true
)));

$form->addElement(new Element\Number("Ordem", "data[ORDEM]", array(
    "value" => (int) $object->getOrdem(),
    "required" => true,
    "min" => 1,
)));

$form->addElement(new Element\Radio("Ativo:", "data[MOSTRAR]", array(1 => 'Sim', 0 => 'Não'), array(
    "value" => $object->getMostrar(),
    "required" => true
)));

if ($object->getNome()) {
    $form->addElement(new Element\HTML('
        <div class="form-group">
        <label class="col-sm-3 control-label">Dados de quem enviou:</label>
        <div class="col-sm-6">
            <ul>
                <li><strong>Nome: </strong>' . $object->getNome() . '</li>
                <li><strong>Email: </strong>' . $object->getEmail() . '</li>
                <li><strong>Data: </strong>' . $object->getDataPergunta('d/m/Y') . '</li>
            </ul>
        </div>
    </div>'));
}



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

    });
</script>
