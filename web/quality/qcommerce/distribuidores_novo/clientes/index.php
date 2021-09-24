<?php
require_once __DIR__ . '/../../includes/security.php';

require __DIR__ . '/../home/actions/index.action.php';

require __DIR__ . '/actions/index.action.php';


//require __DIR__.'/actions/cadastro.action.php';

$title = 'Clientes';

$template = __DIR__ . '/views/index.inc.php';

$script = __DIR__ . '/scripts/scripts.inc.php';


require  __DIR__ . '/../includes/layout.inc.php';
//var_dump(123);die;
