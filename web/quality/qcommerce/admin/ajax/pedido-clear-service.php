<?php

use ClearSale\Ambient\Production;
use ClearSale\Auth\Login;

// TODO: need sandbox
$service = new ClearSale\Service\Orders(
    new Production(),
    new Login('SpiGreen', '9Q82eY7aOP')
);

return $service;
