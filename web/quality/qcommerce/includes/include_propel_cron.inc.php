<?php

require_once $rootDir . '/../vendor/propel/propel1/runtime/lib/Propel.php';

Propel::init($rootDir . '/propel/build/conf/qcommerce-conf.php');

set_include_path($rootDir . '/propel/build/classes' . PATH_SEPARATOR . get_include_path());

//require_once 'qpress/util/QPropelPager.php';
