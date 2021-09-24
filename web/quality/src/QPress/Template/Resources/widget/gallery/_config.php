<?php
switch (Config::get('produto.proporcao')) {

    case '1:1':

        $aspectRatio    = '1x1';
        $resizeThumb    = "width=150&height=150&cropratio=1.0:1";
        $resizeMdImage  = "width=600&height=600&cropratio=1.0:1";
        $resizeLgImage  = "width=1024&height=1024&cropratio=1.0:1";

        break;

    case '4:3':

        $aspectRatio    = '4x3';
        $resizeThumb    = "width=150&height=112&cropratio=1.333:1";
        $resizeMdImage  = "width=600&height=450&cropratio=1.333:1";
        $resizeLgImage  = "width=1024&height=768&cropratio=1.333:1";

        break;

    case '3:4':

        $aspectRatio    = '3x4';
        $resizeThumb    = "width=150&height=200&cropratio=0.75:1";
        $resizeMdImage  = "width=600&height=800&cropratio=0.75:1";
        $resizeLgImage  = "width=768&height=1024&cropratio=0.75:1";

        break;
}