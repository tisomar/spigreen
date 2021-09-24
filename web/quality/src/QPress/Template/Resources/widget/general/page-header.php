<?php
$isAjudaValid = null;

if(isset($_SERVER['REQUEST_URI']) && stripos($_SERVER['REQUEST_URI'], '/minha-conta/') !== false ) {
    include __DIR__ . '/video-central-ajuda.php';
    $array_url = explode('minha-conta/', $_SERVER['REQUEST_URI']);
    $url = $array_url[1];
    if (substr($url, -1) == '/') {
        $url_final = substr_replace($url, "", -1);
    } else {
        $url_final = $url;
    }

    $ajudaPaginaVideo = AjudaPaginaVideoQuery::create()
        ->filterByUrlSlug($url_final)
        ->findOne();

    if ($ajudaPaginaVideo instanceof AjudaPaginaVideo
        && $ajudaPaginaVideo->getVideo() != ''
        && $ajudaPaginaVideo->getVideo() != null
        && ClientePeer::isAuthenticad()
    ) {
        $isAjudaValid = $ajudaPaginaVideo;
    }
}

if(isset($_SERVER['REQUEST_URI']) && stripos($_SERVER['REQUEST_URI'], '/produtos/') !== false ) {
    include __DIR__ . '/video-central-ajuda.php';

    $ajudaPaginaVideo = AjudaPaginaVideoQuery::create()
        ->filterByUrlSlug('produtos/')
        ->findOne();

    if ($ajudaPaginaVideo instanceof AjudaPaginaVideo
        && $ajudaPaginaVideo->getVideo() != ''
        && $ajudaPaginaVideo->getVideo() != null
        && ClientePeer::isAuthenticad()
    ) {
        $isAjudaValid = $ajudaPaginaVideo;
    }
}

$colPage = 12;

if(!is_null($isAjudaValid)){
    $colPage = 10;
}

?>

<div class="container">
    <div class="page-header col-xs-<?php echo $colPage; ?>">
        <h1 <?php echo isset($titleClass) ? 'class="' . $titleClass . '"' : ''; ?>>
            <?php echo $title; ?>
            <?php if (isset($subtitle) && $subtitle != ''): ?>
                <small><?php echo $subtitle; ?></small>
            <?php endif; ?>
        </h1>
    </div>
    <?php if(!is_null($isAjudaValid)): ?>
        <div class="page-header col-xs-2 ">
            <a href="#" data-toggle="modal" data-target="#video-ajuda" class="pull-right" style="color: #A1E63A;"
               title="central de ajuda">
                <i class="fa fa-question-circle fa-3x"></i>
            </a>
        </div>
    <?php endif; ?>
</div>

<?php if(!is_null($isAjudaValid)): ?>
    <script>
        src="<?php echo $isAjudaValid->getVideo()?>";

        $(function(){
            $.post('<?php echo ROOT_PATH . '/minha-conta/actions/cliente-central-ajuda-view.php'; ?>',
                {videoId: '<?php  echo $isAjudaValid->getId()?>', sistema: '<?php  echo $isAjudaValid->getSistema()?>'},
                function (response) {
                    if(response.visto == false){
                        $('#video-ajuda').modal('show');
                    }
                },
                'json'
            )
        });

        $(function(){
            $('#video-ajuda').on('show.bs.modal', function (e) {
                $iframe = $(this).find("iframe");
                $iframe.attr("src", src);
            });
        });


    </script>
<?php endif;?>