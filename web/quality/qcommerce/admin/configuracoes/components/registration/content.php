<?php
/* @var $object Parametro */

use PFBC\Form;
use PFBC\Element;

if ($isLightbox && count($erros) == 0 && $container->getRequest()->getMethod() == 'POST') {
    ?>
    <hr>Esta janela fechará em <span class="sec">5</span> segundos...</h3>
    <script type="text/javascript">
        $(function() {
            function setTime() {
                --totalSeconds;
                $('.sec').text(totalSeconds);
            }

            var totalSeconds = 5;
            setInterval(setTime, 1000);

            setTimeout(function() {
                parent.$("#modal-iframe").modal('hide');
            }, totalSeconds * 1000);
        });
    </script>
    <?php
} else {
    $form = new Form("registrer");

    $form->configure(array(
        'class' => 'row-border',
        'action' => $request->server->get('REQUEST_URI'),
        'view' => new PFBC\View\Vertical(),
    ));

    switch ($object->getType()) {
        case 'TEXT':
            $form->addElement(new Element\Textbox("", "data[VALOR]", array(
                "value" => $object->getValor(),
            )));

            break;

        case 'MONEY':
            $form->addElement(new Element\Textbox("", "data[VALOR]", array(
                "value" => $object->getValor(),
                "class" => "maskMoney"
            )));

            break;

        case 'TEXTAREA':
            $form->addElement(new Element\Textarea("", "data[VALOR]", array(
                "value" => $object->getValor(),
                "rows" => 14,
            )));

            break;

        case 'EDITOR':
            $form->addElement(new Element\Textarea("", "data[VALOR]", array(
                "value" => $object->getValor(),
                "rows" => 14,
                "class" => "mceEditor"
            )));

            break;

        case 'SELECT':
            $form->addElement(new Element\Select("", "data[VALOR]", json_decode($object->getTypeOptions(), true), array(
                "value" => $object->getValor(),
            )));

            break;

        case 'IMAGE':
            $form = new Form("registrer");

            $form->configure(array(
                'class' => 'row-border',
                'action' => $request->server->get('REQUEST_URI'),
            ));

            $form->addElement(new Element\FileImage("", "VALOR", array(
                "required" => true,
                "dimensions" => array(
                    'width' => '100%',
                    'height' => 'auto',
                )
            )));

            if ($object->isImagemExists()) {
                $form->addElement(new Element\Hidden('data[VALOR]', $object->getValor()));

                $html = '
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="registrer-element-1">
                        Imagem atual:</label>
                        <div class="col-sm-6">
                            ' . $object->getThumb('height=400', array(
                        'class' => 'thumbnail',
                        'style' => 'background: #555',
                    )) . '
                        </div>
                    </div>';

                $form->addElement(new Element\HTML($html));
            }

            break;

        default:
            break;
    }

    $form->addElement(new Element\SaveButton());

    if ($object->isNew() == false) {
        $form->addElement(new Element\Hidden('data[ID]', $object->getId()));
    }

    $form->addElement(new Element\Hidden('redirectToOnSuccess', $config['routes']['list']));


    $form->render();
}
