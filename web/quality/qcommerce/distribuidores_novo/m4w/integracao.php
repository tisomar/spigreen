<?php
require __DIR__ . '/../home/actions/index.action.php';
require_once __DIR__ . '/../../includes/security.php';

require __DIR__ . '/actions/integracao.action.php';
require __DIR__ . '/consultas/m4w.listas.php';

$title = 'Contatos - Exportação para Mail For Web';

$template   = __DIR__ . '/views/integracao.inc.php';
$script     = __DIR__ . '/scripts/scripts.inc.php';

require __DIR__ . '/../includes/layout.inc.php';
