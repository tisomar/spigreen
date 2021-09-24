<?php $objCliente = ClientePeer::getClienteLogado(); /* @var $objCliente Cliente */  ?>
<header class="navbar navbar-fixed-top" style="z-index: 9999;">
    
    <div class="navbar-inner">
        <div class="navbar-brand">
            <a href="<?php echo $root_path ?>/distribuidores_novo" title="Spigreen">

                <picture>
                    <source media="(min-width: 1330px)" srcset="">Logo
                    <img src="#" alt="Spigreen">
                </picture>
            </a>
        </div>

        <?php require __DIR__ . '/../includes/menu.inc.php';  ?>

        <?php require __DIR__ . '/../includes/icons_notificacoes.inc.php';  ?>
    </div>
</header>
