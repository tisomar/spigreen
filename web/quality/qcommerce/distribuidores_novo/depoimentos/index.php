<?php

require_once __DIR__ . '/../../includes/security.php';

require __DIR__ . '/actions/index.action.php';

$title = 'Depoimentos';

$template = __DIR__ . '/views/index.inc.php';

$novoUrl = $root_path . '/distribuidores_novo/depoimentos/convite';
$novoLabel = 'Enviar convite';

require  __DIR__ . '/../includes/layout.inc.php';
