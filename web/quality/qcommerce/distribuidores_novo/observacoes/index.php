<?php

require_once __DIR__ . '/../../includes/security.php';

require __DIR__ . '/actions/index.action.php';

$title = 'Observações';

$template = __DIR__ . '/views/index.inc.php';

$novoUrl = $root_path . '/distribuidores_novo/observacoes/cadastro';

require  __DIR__ . '/../includes/layout.inc.php';
