<?php

$stylesheets = array(
    'css/styles.min.css',
    'plugins/jqueryui/jqueryui.css',
    'plugins/form-toggle/toggles.css',
    'plugins/form-jasnyupload/jasny-bootstrap.css',
    'plugins/pretty-photo/css/prettyPhoto.css',
    'plugins/bootstrap-editable/bootstrap-editable.css',
    'plugins/form-tokenfield/bootstrap-tokenfield.css',
    'plugins/form-tokenfield/tokenfield-typeahead.css',
    'plugins/form-daterangepicker/daterangepicker-bs3.css',
    'plugins/form-datepicker/css/datepicker.css',
    'plugins/form-datepicker/css/datepicker3.css',
    'plugins/form-fseditor/fseditor.css',
    'plugins/form-select2/select2.css',
    'plugins/form-select2/bootstrap-select2.css',
    'plugins/pines-notify/jquery.pnotify.default.css',
    'plugins/bootstrap-select/bootstrap-select.css',
    'plugins/charts-morrisjs/morris.css',
    'plugins/jquery-fileupload/css/jquery.fileupload-ui.css',
    'plugins/form-multiselect/css/multi-select.css',
    'plugins/form-select2/select2.css',
    'plugins/form-select2/bootstrap-select2.css',
);

foreach ($stylesheets as $src) {
    echo sprintf("<link rel='stylesheet' type='text/css' href='%s' />\n", asset('/admin/assets/' . $src));
}

echo sprintf("<link rel='stylesheet' type='text/css' href='%s' media='screen,print' />\n", asset('/admin/assets/css/print.css'));

echo "<script type='text/javascript' src='" . asset('/admin/assets/js/jquery-1.10.2.min.js') . "'></script>";
