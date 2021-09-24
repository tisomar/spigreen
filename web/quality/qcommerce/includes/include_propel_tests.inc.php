<?php
Propel::init(QCOMMERCE_DIR . '/propel/build/conf/qcommerce-tests-conf.php');

set_include_path(QCOMMERCE_DIR . '/propel/build/classes' . PATH_SEPARATOR . get_include_path());

require_once 'qpress/util/QPropelPager.php';
