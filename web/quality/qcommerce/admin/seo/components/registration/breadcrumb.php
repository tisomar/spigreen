<?php

use QPress\Breadcrumb\Breadcrumb;

$bc = new Breadcrumb();
$bc->add('In&iacute;cio');
$bc->add('Marketing');
$bc->add('SEO');
$bc->render();
