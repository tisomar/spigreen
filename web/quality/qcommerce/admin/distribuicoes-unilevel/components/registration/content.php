<?php
/* @var $object Distribuicao */
use PFBC\Form;
use PFBC\Element;

$form = new Form("registrer");

$form->configure(array(
    'class' => 'row-border',
    'action' => $request->server->get('REQUEST_URI')
));

$form->addElement(new Element\HTML(
    '<div class="form-group">
        <label class="col-sm-3 control-label">Data Geração</label>
        <div class="col-sm-6">
            <div class="input-group">
                <input type="text" name="data[DATA]" value="' . (($dt = $object->getData('d/m/Y')) ? $dt : date('d/m/Y')) . '" class="form-control datepicker-today mask-date">
        </div>
    </div>
</div>'
));

/*$form->addElement(new Element\HTML(
    '<div class="form-group">
        <label class="col-sm-3 control-label">Data Início</label>
        <div class="col-sm-6">
            <div class="input-group">
                <input type="text" name="data[DATA_INICIO]"
                    value="' . (($dt = $object->getDataInicio('d/m/Y')) ? $dt : date('d/m/Y')) . '"
                    class="form-control _datepicker mask-date">
        </div>
    </div>
</div>'));

$form->addElement(new Element\HTML(
    '<div class="form-group">
        <label class="col-sm-3 control-label">Data Final</label>
        <div class="col-sm-6">
            <div class="input-group">
                <input type="text" name="data[DATA_FINAL]"
                    value="' . (($dt = $object->getDataFinal('d/m/Y')) ? $dt : date('d/m/Y')) . '"
                    class="form-control _datepicker mask-date">
        </div>
    </div>
</div>'));*/

$form->addElement(new Element\SaveButton());
$form->addElement(new Element\CancelButton($config['routes']['list']));

if ($object->isNew() == false) {
    $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
}

$form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));


$form->render();
