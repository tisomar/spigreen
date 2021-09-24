<?php
$javascripts = array(
    'js/jqueryui-1.10.3.min.js',
    'js/bootstrap.min.js',
    'js/enquire.js',
    'js/jquery.cookkie.js',
    #'js/jquery.touchSwipe.min.js',
    'js/detectmobilebrowser.js',
    'js/jquery.nicescroll.min.js',

    'plugins/tinymce/tinymce.min.js',
    'plugins/bootbox/bootbox.js',
    'plugins/codeprettifier/prettify.js',
    'plugins/easypiechart/jquery.easypiechart.min.js',
    'plugins/sparklines/jquery.sparklines.min.js',
    'plugins/form-jasnyupload/jasny-bootstrap.js',
    'plugins/form-toggle/toggle.min.js',
    'plugins/form-wysihtml5/wysihtml5-0.3.0.min.js',
    'plugins/form-wysihtml5/bootstrap-wysihtml5.js',
    'plugins/bootstrap-editable/bootstrap-editable.js',
    'plugins/tinymce/tinymce.min.js',
    'plugins/pretty-photo/js/jquery.prettyPhoto.js',
    'plugins/form-maskmoney/jquery.maskMoney.min.js',
    'plugins/form-maskmoney/qp.maskMoney.js',
    'plugins/form-tokenfield/bootstrap-tokenfield.js',
//    'plugins/form-maskedinput/jquery.maskedinput.js',
    'plugins/form-mask/jquery.mask.min.js',
    'plugins/form-fseditor/jquery.fseditor-min.js',
    'plugins/form-select2/select2.min.js',

    'plugins/form-daterangepicker/daterangepicker.min.js',
    'plugins/form-daterangepicker/moment.min.js',

    'plugins/form-datepicker/js/bootstrap-datepicker.js',
    'plugins/form-datepicker/js/locales/bootstrap-datepicker.pt-BR.js',

    'plugins/pines-notify/jquery.pnotify.js',
    'plugins/bootstrap-select/bootstrap-select.js',
    'plugins/quicksearch/jquery.quicksearch.min.js',
    'plugins/form-colorpicker/js/bootstrap-colorpicker.min.js',
    'plugins/form-typeahead/typeahead.min.js',
    'plugins/mixitup/jquery.mixitup.min.js',

    'plugins/jquery-fileupload/js/vendor/jquery.ui.widget.js',
    'plugins/jquery-fileupload/js/tmpl.min.js',
    'plugins/jquery-fileupload/js/load-image.min.js',
    'plugins/jquery-fileupload/js/canvas-to-blob.min.js',
    'plugins/jquery-fileupload/js/jquery.blueimp-gallery.min.js',
    'plugins/jquery-fileupload/js/jquery.fileupload.js',
    'plugins/jquery-fileupload/js/jquery.fileupload-process.js',
    'plugins/jquery-fileupload/js/jquery.fileupload-image.js',
    'plugins/jquery-fileupload/js/jquery.fileupload-audio.js',
    'plugins/jquery-fileupload/js/jquery.fileupload-video.js',
    'plugins/jquery-fileupload/js/jquery.fileupload-validate.js',
    'plugins/jquery-fileupload/js/jquery.fileupload-ui.js',

    'plugins/form-multiselect/js/jquery.multi-select.min.js',
    'plugins/quicksearch/jquery.quicksearch.min.js',
    'plugins/form-typeahead/typeahead.min.js',
    'plugins/form-select2/select2.min.js',

    'js/placeholdr.js',
    'js/application.js',
);

foreach ($javascripts as $src) {
    echo sprintf("<script type='text/javascript' src='%s'></script>", asset('/admin/assets/' . $src));
}
