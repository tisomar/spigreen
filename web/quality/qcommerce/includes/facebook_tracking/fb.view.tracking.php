<?php

use QPress\Template\Widget;
if (!isLocalhost()) {
    Widget::render('facebook/tracking', array('events' => $events));
}
