<?php

require_once __DIR__ . '/../../includes/security.php';

require __DIR__ . '/actions/integracao.action.php';
require __DIR__ . '/consultas/m4w.export.status.php';

$title = 'Contatos - Exportação para Mail For Web';

$template   = __DIR__ . '/views/integracao.result.inc.php';
$script     = __DIR__ . '/scripts/scripts.inc.php';

require __DIR__ . '/../includes/layout.inc.php';
