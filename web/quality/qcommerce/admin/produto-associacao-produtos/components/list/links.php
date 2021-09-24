<?php

use PFBC\Element;


$back = new Element\BackButton(get_url_admin() . '/produto-associacao/list/?context=' . $context . '&reference=' . $reference, 'Voltar para as associações');
$back->render();
