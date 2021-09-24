<?php

use PFBC\Element;

$add = new \PFBC\Element\HTML('<a href="javascript:void(0);" class="btn btn-primary" onclick="AddTableRow(this)" style="margin-right: 10px;">Adicionar novo produto composto</a>');
$add->render();

$back = new Element\BackButton($config['routes']['list']);
$back->render();
