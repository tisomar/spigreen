
<?php include_once __DIR__ . '/../includes/header.inc.php';  ?>
<body class="page-body"<?php echo (isset($page) ? ' data-page="' . $page . '"' : ''); ?>>
    <div class="page-container horizontal-menu">
        <?php require __DIR__ . '/../includes/topo.inc.php';  ?>
        <div class="main-content container">

            <?php
            FlashMsg::display() ?>
            <?php if (!isset($template)) {
                trigger_error('Template não definido.', E_USER_ERROR);
                exit();
            }
            ?>
            <?php  require $template;?>

        </div>


    </div>

    <?php
    require __DIR__ . '/rodape.inc.php';  ?>

    <?php require __DIR__ . '/includes_rodape.inc.php';  ?>

    <?php if (isset($script)) {
        require $script;
    } ?>

    <script>
        $(document).ready(function(){
            var bloqueado = <?php echo ClientePeer::getBloqueado() ? "true" : "false"; ?>;
            if(bloqueado == true){
                $('a').each(function(i, val){
                    var str = $(this).prop('href');
                    if(!str.includes('central/pontos')){
                        $(this).off('click');
                        $(this).click(function (event){
                            event.preventDefault();
                        });
                    }
                });

                $('#modal-criar-atividade').remove();
                $('td').off('click');
                $('button, td').on('click', function (event) {
                    event.preventDefault();
                });

                $('#bt-menu').remove();

                $('#modal-agenda-bloqueada').modal('show');
            }
        });
    </script>
</body>
</html>
